<?php

/**
 * Zikula Application Framework
 *
 * @copyright  (c) Zikula Development Team
 * @link       http://www.zikula.org
 * @version    $Id: pnadmin.php 202 2009-12-09 20:28:11Z aperezm $
 * @license    GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @author     Albert Pérez Monfort <aperezm@xtec.cat>
 * @category   Zikula_Extension
 * @package    Utilities
 * @subpackage Files
 */
class IWdocmanager_Controller_User extends Zikula_AbstractController {

    public function postInitialize() {
        $this->view->setCaching(false);
    }

    /**
     * Give access to the administrator main page
     * @author:    Albert Pérez Monfort (aperezm@xtec.cat)
     * @return:    The form for general configuration values of the Intraweb modules
     */
    public function main() {
        // Security check
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_ADMIN)) {
            return LogUtil::registerPermissionError();
        }
        return System::redirect(ModUtil::url($this->name, 'user', 'viewDocs'));
    }

    public function viewDocs($args) {
        $categoryId = FormUtil::getPassedValue('categoryId', isset($args['categoryId']) ? $args['categoryId'] : 0, 'GET');
        // Security check
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        $directoriroot = ModUtil::getVar('IWmain', 'documentRoot');
        $documentsFolder = $this->getVar('documentsFolder');

        $categoriesArray = ($categoryId > 0) ? array($categoryId) : array();
        $categories = ModUtil::Func($this->name, 'user', 'getUserCategories', array('accessType' => 'read'));

        $canAdd = false;

        if ($categoryId > 0) {
            // check if user can access to this category
            $canAccess = ModUtil::func($this->name, 'user', 'canAccessCategory', array('categoryId' => $categoryId,
                        'accessType' => 'read',
                    ));
            if (!$canAccess) {
                LogUtil::registerError($this->__('You can not add documents to this category'));
                return System::redirect(ModUtil::url($this->name, 'user', 'viewDocs'));
            }

            // check if user can access to this category
            $canAdd = ModUtil::func($this->name, 'user', 'canAccessCategory', array('categoryId' => $categoryId,
                        'accessType' => 'add'));

            $documents = ModUtil::apiFunc($this->name, 'user', 'getAllDocuments', array('categories' => $categoriesArray));
            foreach ($documents as $document) {
                $extensionIcon['icon'] = '';
                if ($document['fileName'] != '') {
                    $extension = FileUtil::getExtension($document['fileName']);
                    $extensionIcon = ($extension != '') ? ModUtil::func('IWmain', 'user', 'getMimetype', array('extension' => $extension)) : '';
                }
                $documents[$document['documentId']]['extension'] = $extensionIcon['icon'];
            }
        } else {
            $documents = array();
        }

        return $this->view->assign('documents', $documents)
                        ->assign('categories', $categories)
                        ->assign('categoryId', $categoryId)
                        ->assign('canAdd', $canAdd)
                        ->fetch('IWdocmanager_user_viewDocs.tpl');
    }

    public function newDoc($args) {
        $categoryId = FormUtil::getPassedValue('categoryId', isset($args['categoryId']) ? $args['categoryId'] : 0, 'GET');
        // Security check
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        $document = array('documentId' => 0,
            'documentName' => '',
            'description' => '',
            'version' => '',
            'authorName' => '',
            'documentLink' => '',
        );

        $categories = ModUtil::Func($this->name, 'user', 'getUserCategories', array('accessType' => 'add'));

        $extensions = str_replace('|', ', ', ModUtil::getVar('IWmain', 'extensions'));

        return $this->view->assign('document', $document)
                        ->assign('function', 'createDoc')
                        ->assign('extensions', $extensions)
                        ->assign('categories', $categories)
                        ->assign('categoryId', $categoryId)
                        ->fetch('IWdocmanager_user_addEditDoc.tpl');
    }

    public function getUserCategories($args) {
        $parentId = FormUtil::getPassedValue('parentId', isset($args['parentId']) ? $args['parentId'] : 0, 'POST');
        $desc = FormUtil::getPassedValue('desc', isset($args['desc']) ? $args['desc'] : '', 'POST');
        $descLinks = FormUtil::getPassedValue('descLinks', isset($args['descLinks']) ? $args['descLinks'] : '', 'POST');
        $level = FormUtil::getPassedValue('level', isset($args['level']) ? $args['level'] : 0, 'POST');
        $accessType = FormUtil::getPassedValue('accessType', isset($args['accessType']) ? $args['accessType'] : 'read', 'POST');

        // Security check
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_READ)) {
            throw new Zikula_Exception_Forbidden();
        }

        $userGroupsArray = array();

        $uid = UserUtil::getVar('uid');

        //get all the groups of the user
        $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
        $userGroups = ModUtil::func('IWmain', 'user', 'getAllUserGroups', array('uid' => $uid,
                    'sv' => $sv));

        if ($userGroups) {
            foreach ($userGroups as $group) {
                $userGroupsArray[] = $group['id'];
            }
        }

        $categoryData = array();
        $categories = ModUtil::apiFunc($this->name, 'user', 'getAllCategories', array('parentId' => $parentId));

        foreach ($categories as $category) {

            $groups = ($accessType == 'read') ? unserialize($category['groups']) : unserialize($category['groupsAdd']);
           
            if ((count(array_intersect($userGroupsArray, $groups)) > 0) || (UserUtil::isLoggedIn() && in_array(0, $groups)) || (in_array(-1, $groups) && !UserUtil::isLoggedIn()) || SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_ADMIN)) {
                $categoryData[$category['categoryId']] = array('categoryId' => $category['categoryId'],
                    'categoryPath' => $desc . $category['categoryName'],
                    'categoryPathLinks' => $descLinks . $category['categoryName'],
                    'categoryName' => $category['categoryName'],
                    'padding' => $level * 20 . 'px',
                    'description' => $category['description'],
                    'nDocuments' => $category['nDocuments'],
                    'nDocumentsNV' => $category['nDocumentsNV'],
                );

                // Add the options
                $categoryinitData = ModUtil::func($this->name, 'user', 'getUserCategories', array('parentId' => $category['categoryId'],
                            'desc' => $desc . $category['categoryName'] . '/',
                            'descLinks' => $descLinks . '<a href="' . ModUtil::url($this->name, 'user', 'viewDocs', array('categoryId' => $category['categoryId'])) . '">' . $category['categoryName'] . '</a> / ',
                            'level' => $level + 1,
                            'accessType' => $accessType,
                        ));

                if (!empty($categoryinitData)) { // If the menu has items, save them
                    foreach ($categoryinitData as $item) // This foreach converts an n-dimension array in a 1-dimension array, suitable for the template
                        $categoryData[$item['categoryId']] = $item;
                }
            }
        }

        return $categoryData;
    }

    public function createDoc($args) {
        $documentName = FormUtil::getPassedValue('documentName', isset($args['documentName']) ? $args['documentName'] : null, 'POST');
        $categoryId = FormUtil::getPassedValue('categoryId', isset($args['categoryId']) ? $args['categoryId'] : 0, 'POST');
        $documentFile = FormUtil::getPassedValue('documentFile', isset($args['documentFile']) ? $args['documentFile'] : null, 'FILES');
        $documentLink = FormUtil::getPassedValue('documentLink', isset($args['documentLink']) ? $args['documentLink'] : null, 'POST');
        $version = FormUtil::getPassedValue('version', isset($args['version']) ? $args['version'] : null, 'POST');
        $authorName = FormUtil::getPassedValue('authorName', isset($args['authorName']) ? $args['authorName'] : null, 'POST');
        $description = FormUtil::getPassedValue('description', isset($args['description']) ? $args['description'] : null, 'POST');

        // Security check
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_READ)) {
            return LogUtil::registerPermissionError();
        }

        // Confirm authorisation code
        $this->checkCsrfToken();

        // check if user can access to this category
        $canAccess = ModUtil::func($this->name, 'user', 'canAccessCategory', array('categoryId' => $categoryId,
                    'accessType' => 'add'));
        if (!$canAccess) {
            LogUtil::registerError($this->__('You can not add documents to this category'));
            return System::redirect(ModUtil::url($this->name, 'user', 'viewDocs'));
        }

        if ($documentFile['name'] != '') {
            // check if the document have the correct extension
            $allowedExtensionsText = ModUtil::getVar('IWmain', 'extensions');
            $allowed_extensions = explode('|', $allowedExtensionsText);
            $extension = FileUtil::getExtension($documentFile['name']);
            if (!in_array($extension, $allowed_extensions)) {
                LogUtil::registerError($this->__('The document have not the correct extension.'));
                return System::redirect(ModUtil::url($this->name, 'user', 'viewDocs'));
            }
            $documentLink = '';
        }

        $documentLink = (substr($documentLink, 0, 4) != 'http' && $documentLink != '') ? 'http://' . $documentLink : $documentLink;

        $created = ModUtil::apiFunc($this->name, 'user', 'createDoc', array('documentName' => $documentName,
                    'categoryId' => $categoryId,
                    'documentLink' => $documentLink,
                    'version' => $version,
                    'authorName' => $authorName,
                    'description' => $description,
                ));

        if (!$created) {
            LogUtil::registerError($this->__('Error: uploading document'));
            return System::redirect(ModUtil::url($this->name, 'user', 'viewDocs'));
        }

        // update the attached file to the server
        if ($documentFile['name'] != '') {
            $folder = $this->getVar('documentsFolder');
            $sv = ModUtil::func('IWmain', 'user', 'genSecurityValue');
            $update = ModUtil::func('IWmain', 'user', 'updateFile', array('sv' => $sv,
                        'folder' => $folder,
                        'file' => $documentFile,
                        'fileName' => $created . '.' . $extension,
                    ));
            // the function returns the error string if the update fails and and empty string if success
            if ($update['msg'] != '') {
                LogUtil::registerError($update['msg'] . ' ' . $this->__('An error has occurred in the attachment of the file. The document not has been send.'));
            } else {
                // set document name in data base
                ModUtil::apiFunc($this->name, 'user', 'setFileName', array('documentId' => $created,
                    'fileName' => $created . '.' . $extension,
                ));
            }
        }

        // upload the number of documents in category
        ModUtil::apiFunc($this->name, 'user', 'countDocuments', array('categoryId' => $categoryId));

        LogUtil::registerStatus($this->__('The document has been uploaded successfuly'));
        return System::redirect(ModUtil::url($this->name, 'user', 'viewDocs'));
    }

    public function canAccessCategory($args) {
        $categoryId = FormUtil::getPassedValue('categoryId', isset($args['categoryId']) ? $args['categoryId'] : 0, 'POST');
        $accessType = FormUtil::getPassedValue('accessType', isset($args['accessType']) ? $args['accessType'] : 'read', 'POST');

        $userCategories = ModUtil::func($this->name, 'user', 'getUserCategories', array('accessType' => $accessType));

        if (array_key_exists($categoryId, $userCategories)) {
            return true;
        }

        return false;
    }

}
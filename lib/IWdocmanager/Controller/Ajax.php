<?php

class IWdocmanager_Controller_Ajax extends Zikula_Controller_AbstractAjax {

    public function addCategory($args) {
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Fatal($this->__('Sorry! No authorization to access this module.'));
        }

        $categoryId = $this->request->getPost()->get('categoryId', '');
        if (!$categoryId) {
            throw new Zikula_Exception_Fatal($this->__('no category id'));
        }

        $content = ModUtil::func($this->name, 'admin', 'addCategory', array('categoryId' => $categoryId));

        return new Zikula_Response_Ajax(array('categoryId' => $categoryId,
                    'content' => $content,
                ));
    }

    public function editCategory($args) {
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Fatal($this->__('Sorry! No authorization to access this module.'));
        }

        $categoryId = $this->request->getPost()->get('categoryId', '');
        if (!$categoryId) {
            throw new Zikula_Exception_Fatal($this->__('no category id'));
        }

        $content = ModUtil::func($this->name, 'admin', 'editCategory', array('categoryId' => $categoryId));

        return new Zikula_Response_Ajax(array('categoryId' => $categoryId,
                    'content' => $content,
                ));
    }

    public function deleteCategory($args) {
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Fatal($this->__('Sorry! No authorization to access this module.'));
        }

        $categoryId = $this->request->getPost()->get('categoryId', '');
        if (!$categoryId) {
            throw new Zikula_Exception_Fatal($this->__('no category id'));
        }

        $category = ModUtil::apiFunc($this->name, 'user', 'getCategory', array('categoryId' => $categoryId));

        // checks if the category have subcategories
        $have = ModUtil::apiFunc($this->name, 'user', 'haveSubcategories', array('parentId' => $categoryId));
        if ($have) {
            throw new Zikula_Exception_Fatal($this->__('It is not possible to remove this category because it have subcategories. First you have to delete all the subcategories'));
        }

        $deleted = ModUtil::apiFunc($this->name, 'admin', 'deleteCategory', array('categoryId' => $categoryId));
        if (!$deleted) {
            throw new Zikula_Exception_Fatal($this->__('Error deleting the category'));
        }

        return new Zikula_Response_Ajax(array('categoryId' => $categoryId,
                ));
    }

    public function createCategory($args) {
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Fatal($this->__('Sorry! No authorization to access this module.'));
        }

        $categoryId = $this->request->getPost()->get('categoryId', '');
        if (!$categoryId) {
            throw new Zikula_Exception_Fatal($this->__('no category id'));
        }

        $categoryName = $this->request->getPost()->get('categoryName', '');
        $description = $this->request->getPost()->get('description', '');
        $active = $this->request->getPost()->get('active', '');
        $groups = $this->request->getPost()->get('groups', '');
        $groupsAdd = $this->request->getPost()->get('groupsAdd', '');

        $groupsArray = explode('$$', substr($groups, 1, strlen($groups) - 2));
        $groupsAddArray = explode('$$', substr($groupsAdd, 1, strlen($groupsAdd) - 2));

        $groupsString = serialize($groupsArray);
        $groupsAddString = serialize($groupsAddArray);

        $created = ModUtil::apiFunc($this->name, 'admin', 'createCategory', array('categoryName' => $categoryName,
                    'description' => $description,
                    'groups' => $groupsString,
                    'groupsAdd' => $groupsAddString,
                    'active' => $active,
                    'parent' => $categoryId,
                ));

        if (!$created) {
            throw new Zikula_Exception_Fatal($this->__('Error creating the category'));
        }

        $content = ModUtil::Func($this->name, 'admin', 'viewCategoriesContent');

        return new Zikula_Response_Ajax(array('content' => $content,
                ));
    }

    public function updateCategory($args) {
        if (!SecurityUtil::checkPermission('IWdocmanager::', '::', ACCESS_ADMIN)) {
            throw new Zikula_Exception_Fatal($this->__('Sorry! No authorization to access this module.'));
        }

        $categoryId = $this->request->getPost()->get('categoryId', '');
        if (!$categoryId) {
            throw new Zikula_Exception_Fatal($this->__('no category id'));
        }

        $categoryName = $this->request->getPost()->get('categoryName', '');
        $description = $this->request->getPost()->get('description', '');
        $active = $this->request->getPost()->get('active', '');
        $groups = $this->request->getPost()->get('groups', '');
        $groupsAdd = $this->request->getPost()->get('groupsAdd', '');

        $groupsArray = explode('$$', substr($groups, 1, strlen($groups) - 2));
        $groupsAddArray = explode('$$', substr($groupsAdd, 1, strlen($groupsAdd) - 2));

        $groupsString = serialize($groupsArray);
        $groupsAddString = serialize($groupsAddArray);

        $updated = ModUtil::apiFunc($this->name, 'admin', 'updateCategory', array('categoryId' => $categoryId,
                    'items' => array('categoryName' => $categoryName,
                        'description' => $description,
                        'groups' => $groupsString,
                        'groupsAdd' => $groupsAddString,
                        'active' => $active,
                        )));

        if (!$updated) {
            throw new Zikula_Exception_Fatal($this->__('Error updating the category'));
        }

        $content = ModUtil::Func($this->name, 'admin', 'viewCategoriesContent');

        return new Zikula_Response_Ajax(array('content' => $content,
                ));
    }

    public function openDocumentLink($args) {
        $documentId = $this->request->getPost()->get('documentId', '');
        if (!$documentId) {
            throw new Zikula_Exception_Fatal($this->__('no document id'));
        }

        // get document
        $document = ModUtil::apiFunc($this->name, 'user', 'getDocument', array('documentId' => $documentId));
        if (!$document) {
            throw new Zikula_Exception_Fatal($this->__('Document not found.'));
        }

        // count click on document record
        // TODO

        return new Zikula_Response_Ajax(array('href' => $document['documentLink'],
                ));
    }

}
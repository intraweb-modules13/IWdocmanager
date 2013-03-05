{include file="IWdocmanager_user_menu.htm"}
{checkpermission component='IWdocmanager::' instance='::' level='ACCESS_ADMIN' assign='authadmin'}
{checkpermission component='IWdocmanager::' instance='::' level='ACCESS_DELETE' assign='authdelete'}
{checkpermission component='IWdocmanager::' instance='::' level='ACCESS_EDIT' assign='authedit'}
<h2>{gt text="Documents list"}</h2>

{if $categoryId gt 0}
{include file="IWdocmanager_user_viewDocsCatTitle.tpl"}

<div class="categoryListRow" style="margin-bottom: 15px;">
    <div class="categoryNameList">
        {$categories[$categoryId].categoryPathLinks}
    </div>
    <div class="categorynDocumentsList">
        {$categories[$categoryId].nDocuments}{if $authadmin}/<span style="color: red;">{$categories[$categoryId].nDocumentsNV}</span>{/if}
    </div>
    <div class="categoryDescriptionList">
        {$categories[$categoryId].description}
    </div>
    <div class="z-clearer"></div>
</div>
<table class="z-datatable">
    <thead>
        <tr>
            <th>{gt text="Document name"}</th>
            <th>{gt text="Description"}</th>
            <th>{gt text="Version"}</th>
            <th>{gt text="Author"}</th>
            <th>{gt text="Downloads"}</th>
            <th>{gt text="Options"}</th>
        </tr>
    </thead>
    <tbody>
        {foreach item=document from=$documents}
        <tr style="height: 30px;" {if $document.validated eq 0}class="notValiatedDocument"{/if}>
            <td width="200" style="padding: 5px;">
                {if $document.extension neq ''}
                <img src="modules/IWmain/images/fileIcons/{$document.extension}" style="vertical-align: middle;" />
                {elseif $document.documentLink neq ''}
                {img modname='core' src='web.png' set='icons/extrasmall' __alt='link'}
                <img src="images/icons/extrasmall/web.gif" style="vertical-align: middle;" />
                {/if}
                {$document.documentName}
            </td>
            <td style="padding: 5px;">
                {$document.description}
            </td>
            <td width="50" style="padding: 5px;">
                {$document.version}
            </td>
            <td width="100" style="padding: 5px;">
                {$document.authorName}
            </td>
            <td width="50" style="padding: 5px;">
                {$document.nClicks}
            </td>
            <td width="100" style="padding: 5px;">
                {if $authadmin}
                {if $document.fileName eq '' && $document.documentLink eq ''}
                <a href="">
                    {img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Error'}
                </a>
                {else}
                <a href="">
                    {img modname='core' src='button_ok.png' set='icons/extrasmall' __alt='Validate'}
                </a>
                {/if}
                {/if}
                {if $document.fileName != ''}
                <a href="">
                    {img modname='core' src='download.png' set='icons/extrasmall' __alt='Download'}
                </a>
                {elseif $document.documentLink neq ''}
                <a class="z-pointer" onClick="openDocumentLink({$document.documentId});">
                    {img modname='core' src='web.png' set='icons/extrasmall' __alt='Browse website'}
                </a>
                {/if}
                {if $document.fileName neq '' AND $canAdd}
                <a href="">
                    {img modname='core' src='filenew.png' set='icons/extrasmall' __alt='New version'}
                </a>
                {/if}                
                {if $authedit}
                <a href="">
                    {img modname='core' src='xedit.png' set='icons/extrasmall' __alt='Edit'}
                </a>
                {/if}
                {if $authdelete}
                <a href="">
                    {img modname='core' src='14_layer_deletelayer.png' set='icons/extrasmall' __alt='Delete'}
                </a>
                {/if}
            </td>
        </tr>
        {foreachelse}
        <tr>
            <td colspan="10">
                {gt text="No documents found in this category."}
            </td>
        </tr>
        {/foreach}
    </tbody>
</table>

<h3>{gt text="Options legend"}</h3>
<ul>
    {if $authadmin}
    <li>{img modname='core' src='button_ok.png' set='icons/extrasmall' __alt='Validate'} {gt text="The document is pending of validation."}</li>
    <li>{img modname='core' src='button_cancel.png' set='icons/extrasmall' __alt='Error'} {gt text="Error during the upload process. The document must be deleted."}</li>
    {/if}
    <li>{img modname='core' src='download.png' set='icons/extrasmall' __alt='Download'} {gt text="Download document"}</li>
    <li>{img modname='core' src='web.png' set='icons/extrasmall' __alt='Browse'} {gt text="Browse website"}</li>
    {if $canAdd}
    <li>{img modname='core' src='filenew.png' set='icons/extrasmall' __alt='New version'} {gt text="Add a new version of the document"}</li>
    {/if}
    {if $authedit}
    <li>{img modname='core' src='xedit.png' set='icons/extrasmall' __alt='Edit'} {gt text="Edit document information"}</li>
    {/if}
    {if $authdelete}
    <li>{img modname='core' src='14_layer_deletelayer.png' set='icons/extrasmall' __alt='Delete'} {gt text="Delete document"}</li>
    {/if}
</ul>
{else}
{include file="IWdocmanager_user_viewDocsCatTitle.tpl"}
{foreach item=category from=$categories}
<div class="categoryListRow">
    <div class="categoryNameList" style="padding-left: {$category.padding};">
        <a href="{modurl modname='IWdocmanager' type='user' func='viewDocs' categoryId=$category.categoryId}">
            {$category.categoryName}
        </a>
    </div>
    <div class="categorynDocumentsList">
        {$category.nDocuments}{if $authadmin}/<span style="color: red;">{$category.nDocumentsNV}</span>{/if}
    </div>
    <div class="categoryDescriptionList">
        {$category.description}
    </div>
    <div class="z-clearer"></div>
</div>
{foreachelse}
<div>
    {gt text="You can access any category."}
</div>
{/foreach}
{/if}
{include file="IWdocmanager_user_menu.htm"}
<h2>
    {if $document.documentId gt 0}
    {gt text="Edit the document"}: {$document.documentName}
    {else}
    {gt text="Add a new document"}
    {/if}
</h2>
<form  class="z-form" enctype="multipart/form-data" method="post" id="addEdit" action="{modurl modname='IWdocmanager' type='user' func=$function}">
    <input type="hidden" name="documentId" value="{$document.documentId}" />
    <input type="hidden" name="csrftoken" value="{insert name='csrftoken'}" />
    <div class="z-formrow">
        <label for="documentName">{gt text="Document name"}</label>
        <input type="text" id="documentName" name="documentName" value="{$document.documentName}" />
    </div>
    <div class="z-formrow">
        <label for="categoryId">{gt text="Category"}</label>
        <select id="categoryId" name="categoryId" >
            <option value="0">{gt text="Choose a category..."}</option>ç
            {foreach item=category from=$categories}
            <option {if $categoryId eq $category.categoryId}selected="selected"{/if} value="{$category.categoryId}">{$category.categoryPath}</option>
            {/foreach}
        </select>
    </div>
    <div class="z-formrow">
        {if $document.documentId gt 0}
        {if $document.fileOriginalName neq ''}
        <label for="documentFile">{gt text="File"}</label>
        <div class="z-formnote">
            <strong>{$document.fileOriginalName}.{$fileExtension}</strong>
        </div>
        {else}
        <label for="documentFile">{gt text="File link"}</label>
        <input type="text" id="documentLink" name="documentLink" size="70" value="{$document.documentLink}" />
        {/if}
        {else}
        <label for="documentFile">{gt text="File"}</label>
        <input type="file" id="documentFile" name="documentFile" value="" />
        <div class="z-formnote">
            {gt text="or document link"} <input type="text" id="documentLink" name="documentLink" size="70" value="{$document.documentLink}" />
        </div>
        <div class="z-formnote z-informationmsg">
            {gt text="Possible extensions"}: {$extensions}
        </div>
        {/if}
    </div>
    <div class="z-formrow">
        <label for="version">{gt text="Version"}</label>
        <input type="text" id="version" name="version" value="{$document.version}" />
    </div>
    <div class="z-formrow">
        <label for="authorName">{gt text="Author"}</label>
        <input type="text" id="authorName" name="authorName" value="{$document.authorName}" />
        <div class="z-formnote z-warningmsg">
            {gt text="Leave blank if the author is the same person who add the new document"}
        </div>
    </div>
    <div class="z-formrow">
        <label for="description">{gt text="Description"}</label>
        <textarea id="description" name="description">{$document.description}</textarea>
    </div>
    <div class="z-center">
        <span class="z-buttons">
            <a onClick="javascript: document.forms['addEdit'].submit();">
                {img modname='core' src='button_ok.png' set='icons/small'}
                {gt text="Add document"}
            </a>
        </span>
        <span class="z-buttons">
            <a href="{modurl modname='IWdocmanager' type='user' func='viewDocs' categoryId=$categoryId}">
                {img modname='core' src='button_cancel.png' set='icons/small'}
                {gt text="Cancel"}
            </a>
        </span>
    </div>
</form>
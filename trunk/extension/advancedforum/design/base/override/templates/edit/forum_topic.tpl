{* Forum topic - Edit *}

<div class="edit">
    <div class="class-forum-topic">

        <form enctype="multipart/form-data" method="post" action={concat( "/content/edit/", $object.id, "/", $edit_version, "/", $edit_language|not|choose( concat( $edit_language, "/" ), '' ) )|ezurl}>

        <h1>{"Edit %1 - %2"|i18n("extension/advancedforum",,array($class.name|wash,$object.name|wash))}</h1>

        {include uri="design:content/edit_validation.tpl"}

        <input type="hidden" name="MainNodeID" value="{$main_node_id}" />

        <h3>{'Subject'|i18n('extension/advancedforum')}</h3>
        {attribute_edit_gui attribute=$object.data_map.subject}
        <h3>{'Message'|i18n('extension/advancedforum')}</h3>
        {attribute_edit_gui attribute=$object.data_map.message}

        {let current_user=fetch( 'user', 'current_user' )
             sticky_groups=ezini( 'ForumSettings', 'StickyUserGroupArray', 'forum.ini' )}

        {$current_user.groups|contains($sticky)}

            {section var=sticky loop=$sticky_groups}
                {section show=$current_user.groups|contains($sticky)}
                <h3>{'Sticky'|i18n('extension/advancedforum')}</h3>
                {attribute_edit_gui attribute=$object.data_map.sticky}
                {/section}
            {/section}
        {/let}

        <br/>

        <div class="buttonblock">
            <input class="defaultbutton" type="submit" name="PublishButton" value="{'Send for publishing'|i18n('extension/advancedforum')}" />
            <input class="button" type="submit" name="DiscardButton" value="{'Discard'|i18n('extension/advancedforum')}" />
            <input type="hidden" name="DiscardConfirm" value="0" />
        </div>

        </form>
    </div>
</div>



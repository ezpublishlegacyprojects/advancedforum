{* User - Full view *}

<div class="content-view-full">
    <div class="class-user">
    <form method="post" action={"content/action"|ezurl}>
    <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
    <input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
    <input type="hidden" name="ViewMode" value="full" />
    {section show=$node.data_map.image.content}
        <div class="attribute-image">
            {attribute_view_gui attribute=$node.data_map.image alignment=right}
        </div>
    {/section}
    <h1>{$node.name|wash}</h1>

    {section show=$node.object.can_edit}
        <input class="button" type="submit" name="EditButton" value="{'Edit'|i18n('extension/advancedforum')}" />
    {/section}



    <h2>{'Profile'|i18n( 'extension/advancedforum' )}</h2>
    <div class="attribute-long">
        {attribute_view_gui attribute=$node.data_map.profile}
    </div>

    </form>
    </div>
</div>

<div class="content-view-full">
    <div class="class-forum">
<h1>{$node.name|wash(xhtml)}</h1>

    {if $node.data_map.description.has_content}
    <div class="attribute-short">
		{attribute_view_gui attribute=$node.data_map.description}
    </div>
    {/if}
<table class="forum list" cellspacing="0">
    <tr>
       <th>{"Forum"|i18n("extension/advancedforum")}</th>
       <th>{"Posts"|i18n("extension/advancedforum")}</th>
       <th>{"Views"|i18n("extension/advancedforum")}</th>
       <th>{"Last post"|i18n("extension/advancedforum")}</th>
    </tr>
    {def $forums=fetch( content, list, hash( parent_node_id, $node.node_id, sort_by, $node.sort_array, only_translated, true(), 'class_filter_type',  'include', 'class_filter_array', array( 'forum' ) ) )}
    {foreach $forums as $forum sequence array(bglight,bgdark) as $sequence}
	<tr class="{$sequence}">
	<td>

    <div class="attribute-heading">
        <h2><a href={$forum.url_alias|ezurl}>{$forum.name|wash(xhtml)}</a></h2>
    </div>
    {if $forum.data_map.description.has_content}
    <div class="attribute-short">
		{attribute_view_gui attribute=$forum.data_map.description}
    </div>
    {/if}

	</td>
	<td width="1%">{fetch('content','tree_count',hash(parent_node_id,$forum.node_id, 'only_translated', true() ))}</td>
	<td width="1%">{$forum.view_count}</td>
	<td width="30%">
        <ul class="forum-last-posts">
        {def $replys=fetch('content','tree',hash( parent_node_id, $forum.node_id,
        										   'only_translated', true(),
                                                   sort_by, array( array( 'published', false() ) ),
                                                   limit, 1 ) )}

        {foreach $replys as $pre_reply}

        {if $pre_reply.object.class_identifier|eq('forum_reply')}
        {def $reply=$pre_reply.parent}
        {else}
        {def $reply=$pre_reply}
        {/if}
        	<li><h3><a title="{"Posted on %date by %user"|i18n("extension/advancedforum",,hash('%date',$reply.object.published|l10n(shortdatetime),'%user',$reply.object.owner.name))}: {$reply.object.data_map.message|wash(xhtml)}" href={concat( $reply.url_alias, '#msg', $pre_reply.object.id )|ezurl}>{$reply.name|wash(xhtml)}</a></h3></li>
		{/foreach}
        </ul>
    </td>
	</tr>
{/foreach}
</table>
    </div>
</div>
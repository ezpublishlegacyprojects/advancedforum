{if ezini( 'ForumSettings', 'ViewLanguages', 'forum.ini' )} 
{def $languages=ezini( 'ForumSettings', 'ViewLanguages', 'forum.ini' )}
{else}
{def $languages=fetch('content', 'prioritized_language_codes')}
{/if}
{let topic_list=fetch('content','list',hash( parent_node_id, $node.node_id,
                                             limit, 20,
                                             'language', $languages,  
                                             offset, $view_parameters.offset,
                                             sort_by, array( array( attribute, false(), 'forum_topic/sticky' ), array( 'modified_subnode', false() ) ) ) )
     topic_count=fetch('content','list_count', hash( parent_node_id,$node.node_id,
                                                     'language', $languages ))}

<div class="content-view-full">
    <div class="class-forum">
	<div class="page-title">
    	<h1>{$node.name|wash}</h1>
		<h2>{$node.data_map.description.content.output.output_text|strip_tags}</h2>
	</div>
{def $can_create=fetch( 'content', 'access',
                  hash( 'access', 'create',
                        'contentobject', $node,
                        'contentclass_id', 'forum_topic' ) )}

{section show=$can_create|not}
        <p>
        {"You need to be logged in to get access to the forums. You can do so %login_link_start%here%login_link_end%."|i18n( "extension/advancedforum",,
         hash( '%login_link_start%', concat( '<a href=', '/user/login/'|ezurl, '>' ), '%login_link_end%', '</a>' ) )}
        </p>
{/section}

<div class="block">

<div class="left">
    {if $can_create}
    <div class="element">
        <form method="post" action={"content/action/"|ezurl}>
            <input class="button forum-new-topic" type="submit" name="NewButton" value="{'New topic'|i18n( 'extension/advancedforum' )}" />
            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
            <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id.}" />
            <input type="hidden" name="NodeID" value="{$node.node_id}" />
            <input type="hidden" name="ClassIdentifier" value="forum_topic" />
            {if ezini('ForumSettings','CreateLanguage','forum.ini')}
                <input type="hidden" name="ContentLanguageCode" value="{ezini('ForumSettings','CreateLanguage','forum.ini')}" />
            {else}
                <input type="hidden" name="ContentLanguageCode" value="{ezini('RegionalSettings','Locale')}" />
            {/if}
        </form>
    </div>
    {/if}
</div>

<div class="right">
    {def $can_search=fetch( 'user', 'has_access_to',
                    hash( 'module',   'content',
                          'function', 'search' ) )}
    {if $can_search}
    <div class="element">
        <form action="/content/search" method="get">
               <input class="searchtext" type="text" size="10" name="SearchText" value="" />
               <input type="hidden" name="SubTreeArray[]" value="{$node.node_id}" />
               <input type="submit" class="button" value="{'Search'|i18n( 'extension/advancedforum' )}"  />
        </form>
    </div>
    {/if}
    {def $can_notification=fetch( 'user', 'has_access_to',
                    hash( 'module',   'notification',
                          'function', 'use' ) )}
    {if $can_notification}
    <div class="element">
        <form method="post" action={"content/action/"|ezurl}>
            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
            <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id.}" />
            <input class="button forum-keep-me-updated" type="submit" name="ActionAddToNotification" value="{'Keep me updated'|i18n( 'extension/advancedforum' )}" />
            <input type="hidden" name="NodeID" value="{$node.node_id}" />
            <input type="hidden" name="ClassIdentifier" value="forum_topic" />
        </form>
    </div>
    {/if}
    {def $current_user=fetch( 'user', 'current_user' )}
    {if ezini( 'UserSettings', 'AnonymousUserID' )|not($current_user.contentobject_id)}
    <div class="element">
        <form method="get" action={concat("content/view/new/", $node.node_id )|ezurl}>
            <input class="button forum-keep-me-updated" type="submit" name="ActionAddToNotification" value="{'Neues'|i18n( 'extension/advancedforum' )}" />
        </form>
    </div>
    {/if}
</div>

</div>

{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=concat('/content/view','/full/',$node.node_id)
         item_count=$topic_count
         view_parameters=$view_parameters
         item_limit=20}
{if ezini('ForumSettings','Views','forum.ini')|eq('enabled')}
		<script type="text/javascript" charset="utf-8" src={concat('view/count/',$node.node_id)|ezurl}></script>
{/if}
    <div class="content-view-children">

        <table class="list forum" cellspacing="0">
        <tr>
            <th class="topic">
                {"Topic"|i18n( "extension/advancedforum" )}
            </th>
            <th class="replies">
                {"Replies"|i18n( "extension/advancedforum" )}
            </th>
            {if ezini('ForumSettings','Views','forum.ini')|eq('enabled')}
            <th class="views">
                {"Views"|i18n( "extension/advancedforum" )}
            </th>
            {/if}
            <th class="author">
                {"Author"|i18n( "extension/advancedforum" )}
            </th>
            <th class="lastreply">
                {"Last reply"|i18n( "extension/advancedforum" )}
            </th>
        </tr>

        {section var=topic loop=$topic_list sequence=array( bglight, bgdark )}
        {let topic_reply_count=fetch( 'content', 'list_count', hash( parent_node_id, $topic.node_id, 'language', $languages 
) )
             topic_reply_pages=sum( int( div( sum( $topic_reply_count, 1 ), 20 ) ), cond( mod( sum( topic_reply_count, 1 ), 20 )|gt( 0 ), 1, 0 ) )}
        <tr class="{$topic.sequence}">
            <td class="topic">
                <p>{section show=$topic.object.data_map.sticky.content}<img class="forum-topic-sticky" src={"sticky-16x16-icon.gif"|ezimage} height="16" width="16" align="middle" alt="" />{/section}
                <a href={$topic.url_alias|ezurl}>{$topic.object.name|wash|splitlongwords}</a></p>
                {section show=$topic_reply_count|gt( sub( 20, 1 ) )}
                    <p>
                    {'Pages'|i18n( 'extension/advancedforum' )}:
                    {section show=$topic_reply_pages|gt( 5 )}
                        <a href={$topic.url_alias|ezurl}>1</a>...
                        {section var=reply_page loop=$topic_reply_pages offset=sub( $topic_reply_pages, sub( 5, 1 ) )}
                            <a href={concat( $topic.url_alias, '/(offset)/', mul( sub( $reply_page, 1 ), 20 ) )|ezurl}>{$reply_page}</a>
                        {/section}
                    {section-else}
                        <a href={$topic.url_alias|ezurl}>1</a>
                        {section var=reply_page loop=$topic_reply_pages offset=1}
                            <a href={concat( $topic.url_alias, '/(offset)/', mul( sub( $reply_page, 1 ), 20 ) )|ezurl}>{$reply_page}</a>
                        {/section}
                    {/section}
                    </p>
                {/section}
            </td>
            <td class="replies">
                <p>{$topic_reply_count}</p>
            </td>
            {if ezini('ForumSettings','Views','forum.ini')|eq('enabled')}
            <td class="views">
                <p>{$topic.view_count}</p>
            </td>
            {/if}
            <td class="author">
                <div class="attribute-byline">
                   <p class="author">
                   {node_view_gui view='line' content_node=$topic.object.owner.main_node}</p>
                </div>
            </td>
            <td class="lastreply">
            {let last_reply=fetch('content','list',hash( parent_node_id, $topic.node_id,
                                                         'only_translated', true(),'language', $languages,
                                                         sort_by, array( array( 'published', false() ) ),
                                                         limit, 1 ) )}
                {section var=reply loop=$last_reply show=$last_reply}
                <div class="attribute-byline">
                                {section show=$topic_reply_count|gt( 19 )}
                    {def $link=concat( $reply.parent.url_alias, '/(offset)/', sub( $topic_reply_count, mod( $topic_reply_count, 20 ) ) , '#msg', $reply.node_id )}
                {section-else}
                    {def $link=concat( $reply.parent.url_alias, '#msg', $reply.node_id )}
                {/section}
                   <p class="date"><a href={$link|ezurl}>{$reply.object.published|l10n(shortdatetime)}</a></p>
                   <p class="author">
                   {"by"|i18n( "extension/advancedforum" )}
                   {if $reply.object.owner.data_map.nickname.has_content}
                   {$reply.object.owner.data_map.nickname.content|wash}
                   {else}
                   {$reply.object.owner.name|wash}
                   {/if}
                   </p>
                </div>

                {/section}
           {/let}
           </td>
        </tr>
        {/let}
        {/section}
        </table>

    </div>
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=concat('/content/view','/full/',$node.node_id)
         item_count=$topic_count
         view_parameters=$view_parameters
         item_limit=20}
    </div>
</div>



{/let}

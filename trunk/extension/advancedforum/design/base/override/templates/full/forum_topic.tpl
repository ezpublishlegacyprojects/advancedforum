{if ezini( 'ForumSettings', 'ViewLanguages', 'forum.ini' )} 
{def $languages=ezini( 'ForumSettings', 'ViewLanguages', 'forum.ini' )}
{else}
{def $languages=fetch('content', 'prioritized_language_codes')}
{/if}
{let page_limit=20
     reply_limit=cond( $view_parameters.offset|gt( 0 ), 20,
                       19 )
     reply_offset=cond( $view_parameters.offset|gt( 0 ), sub( $view_parameters.offset, 1 ),
                        $view_parameters.offset )
     reply_list=fetch('content','list', hash( parent_node_id, $node.node_id,
                                              limit, $reply_limit,
                                              offset, $reply_offset,
                                              sort_by, array( array( published, true() ) ) ) )
     reply_count=fetch('content','list_count', hash( parent_node_id, $node.node_id ) )
     previous_topic=fetch_alias( subtree, hash( parent_node_id, $node.parent_node_id,
                                                class_filter_type, include,
                                                'language', $languages,  
                                                'only_translated', true(),
                                                class_filter_array, array( 'forum_topic' ),
                                                limit, 1,
                                                attribute_filter, array( and, array( 'published', '<', $node.object.published ) ),
                                                sort_by, array( array( 'published', false() ) ) ) )
     next_topic=fetch_alias( subtree, hash( parent_node_id, $node.parent_node_id,
                                            class_filter_type, include,
                                            'language', $languages,  
                                            'only_translated', true(),
                                            class_filter_array, array( 'forum_topic' ),
                                            limit, 1,
                                            attribute_filter, array( and, array( 'published', '>', $node.object.published ) ),
                                            sort_by, array( array( 'published', true() ) ) ) ) }


<div class="content-view-full">
    <div class="class-forum">
        <h1>{$node.name|wash}</h1>
        {section show=is_unset( $versionview_mode )}
        <div class="content-navigator">
            {section show=$previous_topic}
                <div class="content-navigator-previous">
                    <div class="content-navigator-arrow">&laquo;&nbsp;</div><a href={$previous_topic[0].url_alias|ezurl} title="{$previous_topic[0].name|wash}">{'Previous topic'|i18n( 'extension/advancedforum' )}</a>
                </div>
            {section-else}
                <div class="content-navigator-previous-disabled">
                    <div class="content-navigator-arrow">&laquo;&nbsp;</div>{'Previous topic'|i18n( 'extension/advancedforum' )}
                </div>
            {/section}

            {section show=$previous_topic}
                <div class="content-navigator-separator">|</div>
            {section-else}
                <div class="content-navigator-separator-disabled">|</div>
            {/section}

            {let forum=$node.parent}
                <div class="content-navigator-forum-link"><a href={$forum.url_alias|ezurl}>{$forum.name|wash}</a></div>
            {/let}

            {section show=$next_topic}
                <div class="content-navigator-separator">|</div>
            {section-else}
                <div class="content-navigator-separator-disabled">|</div>
            {/section}

            {section show=$next_topic}
                <div class="content-navigator-next">
                    <a href={$next_topic[0].url_alias|ezurl} title="{$next_topic[0].name|wash}">{'Next topic'|i18n( 'extension/advancedforum' )}</a><div class="content-navigator-arrow">&nbsp;&raquo;</div>
                </div>
            {section-else}
                <div class="content-navigator-next-disabled">
                    {'Next topic'|i18n( 'extension/advancedforum' )}<div class="content-navigator-arrow">&nbsp;&raquo;</div>
                </div>
            {/section}
        </div>

    {def $can_create=fetch( 'content', 'access',
                      hash( 'access', 'create',
                            'contentobject', $node,
                            'contentclass_id', 'forum_reply' ) )}
    
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
                            <input class="button forum-new-reply" type="submit" name="NewButton" value="{'New reply'|i18n( 'extension/advancedforum' )}" />
                            {if ezini('ForumSettings','CreateLanguage','forum.ini')}
                                <input type="hidden" name="ContentLanguageCode" value="{ezini('ForumSettings','CreateLanguage','forum.ini')}" />
                            {else}
                                <input type="hidden" name="ContentLanguageCode" value="{ezini('RegionalSettings','Locale')}" />
                            {/if}
                            <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
                            <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
                            <input type="hidden" name="NodeID" value="{$node.node_id}" />
                            <input type="hidden" name="ClassIdentifier" value="forum_reply" />
                        </form>
                    </div>
                {/if}
            </div>
            <div class="right">
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
                        </form>
                    </div>
                {/if}
            </div>
        </div>
        <div class="break"></div>
        <div class="content-view-children">
            <table class="list forum" cellspacing="0" summary="displays the post list">
                <tr>
                    <th class="author">{"Author"|i18n("extension/advancedforum")}</th>
                    <th class="message">{"Message"|i18n("extension/advancedforum")}</th>
                </tr>
                {section show=$view_parameters.offset|lt( 1 )}
                    <tr>
                        <td class="author">
                            {let owner=$node.object.owner owner_map=$owner.data_map}
                            <p class="author">{node_view_gui view='line' content_node=$node.object.owner.main_node}
                               {section show=is_set( $owner_map.title )}
                                   <br/>{$owner_map.title.content|wash}
                               {/section}
                            </p>
                            {section show=$owner_map.image.has_content}
                                <div class="authorimage">
                                    {attribute_view_gui attribute=$owner_map.image image_class=small}
                                </div>
                            {/section}
        					{def $count=fetch( 'content', 'tree_count', hash( 'parent_node_id', 2,
                           		                                              'attribute_filter', array( array( 'owner', '=', $node.object.owner.id ) ),
                                                                              'class_filter_type',  'include',
                                                                              'class_filter_array', array( 'forum_reply', 'forum_topic' ) ) )}
            
                            <p>{"Posts"|i18n( "extension/advancedforum" )}: {$count}</p>
                                   
                            {section show=is_set( $owner_map.location )}
                               <p>{"Location"|i18n( "extension/advancedforum" )}: {$owner_map.location.content|wash}</p>
                            {/section}
                               
                            {let owner_id=$node.object.owner_id}
                              {section var=author loop=$node.object.author_array}
                                  {section show=eq($owner_id,$author.contentobject_id)|not()}
                                    <p>{"Moderated by"|i18n( "extension/advancedforum" )}: {$author.contentobject.name|wash}</p>
                                  {/section}
                                {/section}
                            {/let}
                            {if ezini('ForumSettings','Views','forum.ini')|eq('enabled')}
                                <script type="text/javascript" charset="utf-8" src={concat('view/count/',$node.node_id)|ezurl}></script>
                            {/if}
                        </td>
                        <td class="message">
                            {if $node.object.can_edit}
                                <form method="post" action={"content/action"|ezurl}>    
                                    <input type="hidden" name="ContentObjectLanguageCode" value="{$node.object.language_codes.0}" />
                                        <div class="object-right">
                                   			<input name="RedirectURI" type="hidden" value="{concat( $node.url_alias,"/(offset)/",first_set( $view_parameters.offset, 0 ),"#msg",$node.object.id)}" />
                                   			<input name="RedirectURIAfterPublish" value="{concat( $node.url_alias,"/(offset)/",first_set( $view_parameters.offset, 0 ),"#msg",$node.object.id)}" type="hidden" />
                    						<input type="hidden" name="ContentObjectID" value="{$node.object.id}" />
                                            <input class="image" title="{'Edit forum message'|i18n('extension/advancedforum')}" src={"edit.gif"|ezimage} name="EditButton" type="image" />
                                        </div>
                       			</form>
                       		{/if}
                       		<h2>{$node.name|wash}</h2>
                            <p class="date">{$node.object.published|l10n(datetime)}</p>  
                            {attribute_view_gui attribute=$node.data_map.message}
                            {if $owner_map.signature.has_content}
                               <div class="attribute-signature">
                                   <p>{$owner_map.signature.content|simpletags|autolink}</p>
                               </div>
                            {/if}
                        </td>
                       {/let}
                    </tr>
                {/section}
                {section var=reply loop=$reply_list sequence=array( bgdark, bglight )}
                    <tr class="{$reply.sequence}">
                        <td class="author">
                            {let owner=$reply.object.owner owner_map=$owner.data_map}
                            <p class="author">{node_view_gui view='line' content_node=$reply.object.owner.main_node}
                            {section show=is_set( $owner_map.title )}
                                <br />{$owner_map.title.content|wash}
                            {/section}</p>
                            {section show=$owner_map.image.has_content}
                                <div class="authorimage">
                                    {attribute_view_gui attribute=$owner_map.image image_class=small}
                                </div>
                            {/section}
        					{def $count=fetch( 'content', 'tree_count', hash( 'parent_node_id', 2, 
        					                                                  'attribute_filter', array( array( 'owner', '=', $reply.object.owner.id ) ),
                                                                              'class_filter_type',  'include',
                                                                              'class_filter_array', array( 'forum_reply', 'forum_topic' ) ) )}
        
                            <p>{"Posts"|i18n( "extension/advancedforum" )}: {$count}</p>
        
                            {section show=is_set( $owner_map.location )}
                                <p>{"Location"|i18n( "extension/advancedforum" )}: {$owner_map.location.content|wash}</p>
                            {/section}
                            {let owner_id=$reply.object.owner.id}
                                {section var=author loop=$reply.object.author_array}
                                    {section show=ne( $reply.object.owner_id, $author.contentobject_id )}
                                        <p>
                                            {'Moderated by'|i18n( 'extension/advancedforum' )}: {$author.contentobject.name|wash}
                                        </p>
                                    {/section}
                                {/section}
                            {/let}
                        </td>
                        <td class="message">
                       	    {if $reply.object.can_edit}
                       		    <form method="post" action={"content/action"|ezurl}>
                                    <input type="hidden" name="ContentObjectLanguageCode" value="{$reply.object.language_codes.0}" />
                                    <div class="object-right">
                                        <input type="hidden" name="ContentObjectID" value="{$reply.object.id}" />
                                        <input name="RedirectURIAfterPublish" value="{concat( $node.url_alias,"/(offset)/",first_set( $view_parameters.offset, 0 ),"#msg",$reply.object.id)}" type="hidden" />
                                        <input class="image" title="{'Edit forum message'|i18n('extension/advancedforum')}" src={"edit.gif"|ezimage} name="EditButton" type="image" />
                                    </div>
                   			    </form>
                   			{/if}
                   			{if $reply.name}
                                <h2 id="msg{$reply.node_id}">{$reply.name|wash}</h2>
                            {/if}
                            <p class="date">{$reply.object.published|l10n( datetime )}</p>
                            {attribute_view_gui attribute=$reply.data_map.message}
                            {if $owner_map.signature.has_content}
                                <div class="attribute-signature">
                                    <p>{$owner_map.signature.content|simpletags|autolink}</p>
                                </div>
                            {/if}
                            {/let}
                        </td>
                    </tr>
                {/section}
           </table>
        </div>
    </div>
</div>
{include name=navigator
         uri='design:navigator/google.tpl'
         page_uri=$node.url_alias
         item_count=$reply_count
         view_parameters=$view_parameters
         item_limit=$page_limit}
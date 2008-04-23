{if $node.data_map.nickname.has_content}
{def $name=$node.data_map.nickname.content}
{else}
{def $name=$node.name}
{/if}
{if $node.object.data_map.profile.has_content}
<a href={$node.url_alias|ezurl} title="{"View profile"|i18n("extension/advancedforum")}"><abbr title="{$name|wash(xhtml)}">{$name|shorten( ezini( 'ForumSettings', 'UsernameShortening', 'forum.ini'), '...' , 'middle' )|wash(xhtml)}</abbr></a>
{else}
<abbr title="{$name|wash(xhtml)}">{$name|shorten( ezini( 'ForumSettings', 'UsernameShortening', 'forum.ini'), '...' , 'middle' )|wash(xhtml)}</abbr>
{/if}
{unset}
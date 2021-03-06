{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{let use_url_translation=ezini('URLTranslator','Translation')|eq('enabled')
     is_update=false()}
{section loop=$object.versions}{section show=and($:item.status|eq(3),$:item.version|ne($object.current_version))}{set is_update=true()}{/section}{/section}

{set-block scope=root variable=subject}{$object.main_node.parent.name|wash} [{ezini("SiteSettings","SiteURL")} - {$object.main_node.parent.name|wash}]{/set-block}
{set-block scope=root variable=from}{concat($object.owner.name,' <', $sender, '>')}{/set-block}
{set-block scope=root variable=message_id}{concat('<node.',$object.main_node_id,'.eznotification','@',ezini("SiteSettings","SiteURL"),'>')}{/set-block}
{set-block scope=root variable=reply_to}{concat('<node.',$object.main_node.parent_node_id,'.eznotification','@',ezini("SiteSettings","SiteURL"),'>')}{/set-block}
*{$object.name|wash} - {$object.published|l10n(datetime)}, {$object.owner.name|wash}

{$object.data_map.message.content.output.output_text|strip_tags}

{"The full forum thread can be read at"|i18n('design/standard/notification')}
http://{ezini("SiteSettings","SiteURL")}{$object.main_node.url_alias|ezurl(no)}?msg{$object.id}


{"If you do not wish to continue receiving these notifications,
change your settings at:"|i18n('design/standard/notification')}
http://{ezini("SiteSettings","SiteURL")}{concat("notification/settings/")|ezurl(no)}

-- 
{"%sitename notification system"
 |i18n('design/standard/notification',,
       hash('%sitename',ezini("SiteSettings","SiteURL")))}
{/let}

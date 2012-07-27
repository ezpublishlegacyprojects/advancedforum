{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{let use_url_translation=ezini('URLTranslator','Translation')|eq('enabled')
     is_update=false()}
{section loop=$object.versions}{section show=and($:item.status|eq(3),$:item.version|ne($object.current_version))}{set is_update=true()}{/section}{/section}
{if $object.owner.data_map.nickname.has_content}
{def $temp_name = $object.owner.data_map.nickname.data_text}
{else}
{def $temp_name = $object.owner.name|wash()}
{/if}

{set-block scope=root variable=subject}{$object.main_node.parent.name|wash} [{ezini("SiteSettings","SiteURL")} - {$object.main_node.parent.name|wash}]{/set-block}
{set-block scope=root variable=from}{concat($temp_name,' <', $sender, '>')}{/set-block}
{set-block scope=root variable=message_id}{concat('<node.',$object.main_node_id,'.eznotification','@',ezini("SiteSettings","SiteURL"),'>')}{/set-block}
{set-block scope=root variable=reply_to}{concat('<node.',$object.main_node.parent_node_id,'.eznotification','@',ezini("SiteSettings","SiteURL"),'>')}{/set-block}
*{$object.name|wash} - {$object.published|l10n(datetime)}, {$temp_name}

{$object.data_map.message.content.output.output_text|strip_tags}
{if contains($object.current_language,'-'}
{def $subStrings=$object.current_language|explode('-'}
{def $siteaccess=concat($subString[0].'_'.$substring[1])}
{undef $subStrings}
{else}
{switch match=$object.current_language}
{case match='ger'}
{def $siteaccess='ger_DE'}
{/case}
{case match='fre'}
{def $siteaccess='fre_FR'}
{/case}
{case match='dut'}
{def $siteaccess='dut_NL'}
{/case}
{case match='ita'}
{def $siteaccess='ita_IT'}
{/case}
{case match='por'}
{def $siteaccess='por_PT'}
{/case}
{case}
{def $siteaccess='other'}
{/case}
{/switch}
{/if}

{"The full forum thread can be read at"|i18n('design/standard/notification')}
http://{ezini("SiteSettings","SiteURL")}/{$siteaccess}{$object.main_node.url_alias|ezurl(no)}?msg{$object.id}


{"If you do not wish to continue receiving these notifications,
change your settings at:"|i18n('design/standard/notification')}
http://{ezini("SiteSettings","SiteURL")}/region/index?URL={concat("notification/settings/")|ezurl(no)}

-- 
{"%sitename notification system"
 |i18n('design/standard/notification',,
       hash('%sitename',ezini("SiteSettings","SiteURL")))}
{/let}

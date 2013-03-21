{if version_compare($smarty.const._PS_VERSION_,'1.5','>')}
	{assign var=cms value="{$base_dir}index.php?id_cms={$page}&controller=cms"}
{else}
	{assign var=cms value="{$base_dir}cms.php?id_cms={$page}"}
{/if}
<div id="cookie_law_informations" style="background: {$bg}; color: {$color};">
	<p>{l s='This website uses cookies' mod='imp_cookies'} <a href="#" class="accept exclusive">{l s='Ok, I get it' mod='imp_cookies'}</a> <a href="{$cms}" class="button_large">{l s='More informations' mod='imp_cookies'}</a></p>
</div><!-- // cookie_law_informations -->
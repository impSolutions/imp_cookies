
<link href="{$module_dir}imp_cookies.css" rel="stylesheet" type="text/css" media="all" />
<script type="text/javascript">
$(function()
{ldelim}
	var imp_cookies_bar = $('#cookie_law_informations');
	var imp_cookies_bar_margin = -imp_cookies_bar.height()-imp_cookies_bar.outerHeight()+'px';
    imp_cookies_bar.css( {ldelim}marginTop: imp_cookies_bar_margin{rdelim}).insertBefore('body > div:first-child');
    imp_cookies_bar.show().delay(800).animate( {ldelim}marginTop: "0px"{rdelim});
    $('#cookie_law_informations .accept').click(function()
    {ldelim}   
        $.ajax({ldelim} url: '{$module_dir}ajax.php', async: false {rdelim});
        imp_cookies_bar.animate( {ldelim}marginTop: imp_cookies_bar_margin{rdelim});
    {rdelim});
{rdelim});
</script>
<div id="cookie_law_informations" style="background: {$bg}; color: {$color};">
	<p>{l s='This website uses cookies' mod='imp_cookies'} <a href="#" class="accept exclusive">{l s='Ok, I get it' mod='imp_cookies'}</a> <a href="{$base_dir}cms.php?id_cms={$page}" class="button_large">{l s='More informations' mod='imp_cookies'}</a></p>
</div><!-- // cookie_law_informations -->
<script type="text/javascript">
$(function()
{ldelim}
    var imp_cookies_bar = $('#cookie_law_informations');
    imp_cookies_bar.insertBefore('body > div:first-child');
    var ieOpacity = Math.floor({$opacity} * 255).toString(16);
     $('.cookie-inner').css({ldelim}
        "filter" : "progid:DXImageTransform.Microsoft.gradient(startColorstr=#"+ieOpacity+"{$bg}, endColorstr=#"+ieOpacity+"{$bg})",
        "-ms-filter" : "progid:DXImageTransform.Microsoft.gradient(startColorstr=#"+ieOpacity+"{$bg}, endColorstr=#"+ieOpacity+"{$bg})"
    {rdelim});      
    $('#cookie_law_informations .accept').click(function()
    {ldelim}   
        $.ajax({ldelim} url: '{$module_dir}ajax.php', async: false {rdelim});
        imp_cookies_bar.remove();
        return false;
    {rdelim});
{rdelim});
</script>
{if version_compare($smarty.const._PS_VERSION_,'1.5','>')}
	{assign var=cookie_cms value="{$base_dir}index.php?id_cms={$page}&controller=cms"}
{else}
	{assign var=cookie_cms value="{$base_dir}cms.php?id_cms={$page}"}
{/if}
<div id="cookie_law_informations" style="position: fixed; {$position}: 0; left: 0; right: 0; text-align: center; z-index: {$zindex};">
    <div class="cookie-inner" style="text-align: {$text_align};display: inline-block;color: {$color}; width: {$width}; background: rgba({$bg_rgb.0},{$bg_rgb.1},{$bg_rgb.2}, {$opacity}); padding: {$padding}; border-radius: {$radius}; margin: {$margin};
    ">
	{$text} <a href="#" class="accept exclusive">{l s='Ok, I get it' mod='imp_cookies'}</a> <a href="{$cookie_cms}" class="button_large">{l s='More informations' mod='imp_cookies'}</a>
    </div>
</div><!-- // cookie_law_informations -->
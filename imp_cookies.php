<?php
/**
	@author: Krystian Podemski, impSolutions.pl
	@release: 03.2013
	@version: 1.1.0
	@desc: UE cookies restrictions? No problem now
**/
if (!defined('_PS_VERSION_'))
	exit;

class imp_cookies extends Module
{

	public function __construct()
	{
		$this->name = 'imp_cookies';
		if (version_compare(_PS_VERSION_, '1.4', '>'))
			$this->tab = 'front_office_features';
		else
			$this->tab = 'impSolutions';

		$this->version = '1.2.0';

		if (version_compare(_PS_VERSION_, '1.4', '>'))
			$this->author = 'impSolutions.pl';

		parent::__construct();

		$this->displayName = $this->l('Cookies Law Informations');
		$this->description = $this->l('Ask your customer to either accept cookies or decline them');
	}

	public function install()
	{
		if (!parent::install()
			OR !$this->registerHook('top')
			OR !$this->registerHook('header')
			OR !Configuration::updateValue('COOKIE_LAW_CMS', '1')
			OR !Configuration::updateValue('COOKIE_LAW_BAR_BG', '#333333')
			OR !Configuration::updateValue('COOKIE_LAW_TEXT_COLOR', '#f0f0f0'))
			return false;
		return true;
	}


	public function uninstall()
	{
		if (!Configuration::deleteByName('COOKIE_LAW_CMS')
			OR !Configuration::deleteByName('COOKIE_LAW_BAR_BG')
			OR !Configuration::deleteByName('COOKIE_LAW_TEXT_COLOR')
			OR !parent::uninstall())
			return false;
		return true;
	} 

	public function hookTop()
	{
		global $smarty, $cookie;

		$bots = array(
            'Googlebot',
            'Baiduspider',
            'ia_archiver',
            'R6_FeedFetcher',
            'NetcraftSurveyAgent',
            'Sogou web spider',
            'bingbot',
            'Yahoo! Slurp',
            'facebookexternalhit',
            'PrintfulBot',
            'msnbot',
            'Twitterbot',
            'UnwindFetchor',
            'urlresolver',
            'Butterfly',
            'TweetmemeBot' );
 
	    foreach($bots as $bot)
	         if( stripos( $_SERVER['HTTP_USER_AGENT'], $bot ) !== false )
	                return;
                   
        if($cookie->__isset('cookieAccepted'))
             return;

		$smarty->assign(
			array(
				'page' => Configuration::get('COOKIE_LAW_CMS'),
				'bg' => Configuration::get('COOKIE_LAW_BAR_BG'),
				'color' => Configuration::get('COOKIE_LAW_TEXT_COLOR'),
			));


		if (version_compare(_PS_VERSION_, '1.4', '<')) 
			return $this->display(__FILE__,'imp_cookies-older.tpl');
		else
			return $this->display(__FILE__,'imp_cookies.tpl');
	}

	public function hookHeader()
	{
		global $cookie;

		if($cookie->__isset('cookieAccepted'))
             return;

		if (version_compare(_PS_VERSION_, '1.5', '>'))
		{
			$this->context->controller->addCSS(($this->_path).'imp_cookies.css', 'all');
		}
		elseif(version_compare(_PS_VERSION_, '1.4', '>'))
		{
			Tools::addCSS(($this->_path).'imp_cookies.css', 'all');
		}
		else return;
	}
      
    public function getContent()
    {
    	$this->_html = '';

    	if(Tools::isSubmit('submitSettings'))
    	{
    		foreach($_POST as $key => $value)
    		{
    			Configuration::updateValue($key,$value);
    		}
    		$this->_html .= $this->displayConfirmation($this->l('Success'));
    	}

    	if (version_compare(_PS_VERSION_, '1.5', '>'))
		{
    		$this->_html .= '<script type="text/javascript" src="../js/jquery/plugins/jquery.colorpicker.js"></script>';
    	}
    	else
    	{
    		$this->_html .= '<script type="text/javascript" src="../js/jquery/jquery-colorpicker.js"></script>';
    	}
    	$this->_html .= '<h2>'.$this->displayName.'</h2>';
    	$this->_html .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">';
    	$this->_html .= '<fieldset>';
    	$this->_html .= '<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>';

    	#page
    	$cms_pages = CMS::listCms();
    	$this->_html .= '<label>'.$this->l('CMS page for the "more informations" link').'</label><div class="margin-form">';
    	$this->_html .= '<select name="COOKIE_LAW_CMS">';
    	foreach($cms_pages as $page):
    		$selected = Configuration::get('COOKIE_LAW_CMS') == $page['id_cms'] ? 'selected="selected"' : '';
    		$this->_html .= '<option value="'.$page['id_cms'].'" '.$selected.'>'.$page['meta_title'].'</option>';
		endforeach;
		$this->_html .= '</select></div>';

		# background
		$this->_html .= '<label>'.$this->l('Background color').'</label><div class="margin-form">';
		$this->_html .= '<input type="color" name="COOKIE_LAW_BAR_BG" data-hex="true" class="color mColorPickerInput" value="'.Configuration::get('COOKIE_LAW_BAR_BG').'" /></div>';

		# text color
		$this->_html .= '<label>'.$this->l('Text color').'</label><div class="margin-form">';
		$this->_html .= '<input type="color" name="COOKIE_LAW_TEXT_COLOR" data-hex="true" class="color mColorPickerInput" value="'.Configuration::get('COOKIE_LAW_TEXT_COLOR').'" /></div>';

		$this->_html .= '<div class="margin-form">';
		$this->_html .= '<input type="submit" value="'.$this->l('Save').'" name="submitSettings" class="button">';
		$this->_html .= '</div>';

    	$this->_html .= '</fieldset>';
    	$this->_html .= '</form>';

    	$this->_html .= '<div style="width: 351px; margin: 20px auto">';
    	$this->_html .= '<a href="http://www.facebook.com/impSolutionsPL" title="" target="_blank"><img alt="" src="'.$this->_path.'impsolutions.png" /></a>';
    	$this->_html .= '</div>';



    	return $this->_html;
    }  
}

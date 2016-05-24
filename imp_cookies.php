<?php
/**
    @author: Krystian Podemski, impSolutions.pl
    @release: 05.2016
    @version: 2.0.0
    @desc: UE cookies restrictions? No problem now
**/
if (!defined('_PS_VERSION_')) {
    exit;
}

class imp_cookies extends Module
{

    public function __construct()
    {
        $this->name = 'imp_cookies';
        $this->tab = 'front_office_features';
       
        $this->version = '2.0.0';

        $this->author = 'impSolutions.pl';

        parent::__construct();

        $this->displayName = $this->l('Cookies Law Informations');
        $this->description = $this->l('Ask your customer to either accept cookies or decline them');

        $this->ps_versions_compliancy = array('min' => '1.6.0.4', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!parent::install()
            or !$this->registerHook('displayTop')
            or !$this->registerHook('displayHeader')
            or !Configuration::updateValue('COOKIE_LAW_CMS', '1')
            or !Configuration::updateValue('COOKIE_LAW_BAR_BG', '#333333')
            or !Configuration::updateValue('COOKIE_LAW_BAR_OPACITY', '0.8')
            or !Configuration::updateValue('COOKIE_LAW_BAR_POSITION', 'top')
            or !Configuration::updateValue('COOKIE_LAW_BAR_WIDTH', '60%')
            or !Configuration::updateValue('COOKIE_LAW_BAR_PADDING', '20px')
            or !Configuration::updateValue('COOKIE_LAW_BAR_RADIUS', '10px')
            or !Configuration::updateValue('COOKIE_LAW_BAR_MARGIN', '20px 0 0 0')
            or !Configuration::updateValue('COOKIE_LAW_TEXT_ALIGN', 'left')
            or !Configuration::updateValue('COOKIE_LAW_BAR_ZINDEX', '999')
            or !$this->putBarText()
            or !Configuration::updateValue('COOKIE_LAW_TEXT_COLOR', '#f0f0f0')) {
            return false;
        }
        return true;
    }

    public function putBarText()
    {
        $languages = Language::getLanguages(true);

        foreach ($languages as $lang) {
            if ($lang['iso_code'] == 'pl') {
                Configuration::updateValue('LAW_MESSAGE', array($lang['id_lang'] => 'Ta strona używa cookies'));
            } else {
                Configuration::updateValue('LAW_MESSAGE', array($lang['id_lang'] => 'This website uses cookies'));
            }
        }

        return true;
    }

    public function uninstall()
    {
        if (!Configuration::deleteByName('COOKIE_LAW_CMS')
            or !Configuration::deleteByName('COOKIE_LAW_BAR_BG')
            or !Configuration::deleteByName('COOKIE_LAW_TEXT_COLOR')
            or !Configuration::deleteByName('COOKIE_LAW_BAR_OPACITY')
            or !Configuration::deleteByName('COOKIE_LAW_BAR_POSITION')
            or !Configuration::deleteByName('COOKIE_LAW_BAR_WIDTH')
            or !Configuration::deleteByName('COOKIE_LAW_BAR_PADDING')
            or !Configuration::deleteByName('COOKIE_LAW_BAR_RADIUS')
            or !Configuration::deleteByName('COOKIE_LAW_BAR_MARGIN')
            or !Configuration::deleteByName('COOKIE_LAW_TEXT_ALIGN')
            or !Configuration::deleteByName('COOKIE_LAW_BAR_ZINDEX')
            or !Configuration::deleteByName('LAW_MESSAGE')
            or !parent::uninstall()) {
            return false;
        }
        return true;
    }

    public function hookDisplayTop()
    {
        $cookie = $this->context->cookie;
        $smarty = $this->context->smarty;

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
 
        foreach ($bots as $bot) {
            if (stripos($_SERVER['HTTP_USER_AGENT'], $bot) !== false) {
                return;
            }
        }

        if ($cookie->__isset('cookieAccepted')) {
            return;
        }

        $smarty->assign(
            array(
                'page' => Configuration::get('COOKIE_LAW_CMS'),
                'bg_rgb' => self::hex2rgb(Configuration::get('COOKIE_LAW_BAR_BG')),
                'bg' => str_replace('#', '', Configuration::get('COOKIE_LAW_BAR_BG')),
                'position' => Configuration::get('COOKIE_LAW_BAR_POSITION'),
                'opacity' => Configuration::get('COOKIE_LAW_BAR_OPACITY'),
                'width' => Configuration::get('COOKIE_LAW_BAR_WIDTH'),
                'padding' => Configuration::get('COOKIE_LAW_BAR_PADDING'),
                'radius' => Configuration::get('COOKIE_LAW_BAR_RADIUS'),
                'margin' => Configuration::get('COOKIE_LAW_BAR_MARGIN'),
                'color' => Configuration::get('COOKIE_LAW_TEXT_COLOR'),
                'zindex' => Configuration::get('COOKIE_LAW_BAR_ZINDEX'),
                'text' => Configuration::get('LAW_MESSAGE', $this->context->language->id),
                'text_align' => Configuration::get('COOKIE_LAW_TEXT_ALIGN'),
            )
        );

        return $this->display(__FILE__, 'imp_cookies.tpl');
    }

    public function hookDisplayHeader()
    {
        $cookie = $this->context->cookie;

        if ($cookie->__isset('cookieAccepted')) {
            return;
        }

        $this->context->controller->addCSS(($this->_path).'imp_cookies.css', 'all');
    }

    public static function hex2rgb($hex)
    {
        $hex = str_replace('#', '', $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1).substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1).substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1).substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        return array($r, $g, $b);
    }
      
    public function getContent()
    {
        $defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
        $languages = Language::getLanguages(true);
        
        $this->_html = '';

        if (Tools::isSubmit('submitSettings')) {
            foreach ($_POST as $key => $value) {
                if (preg_match('/LAW_MESSAGE_/i', $key)) {
                    continue;
                }
                Configuration::updateValue($key, $value);
            }

            $message_trads = array();
            foreach ($_POST as $key => $value) {
                if (preg_match('/LAW_MESSAGE_/i', $key)) {
                    $id_lang = preg_split('/LAW_MESSAGE_/i', $key);
                    $message_trads[(int)$id_lang[1]] = $value;
                }
            }
            Configuration::updateValue('LAW_MESSAGE', $message_trads, true);

            $this->_html .= $this->displayConfirmation($this->l('Success'));
        }

        $this->context->controller->addJqueryPlugin('colorpicker');

        $this->_html .= '<h2>'.$this->displayName.'</h2>';
        $this->_html .= '<form action="'.Tools::safeOutput($_SERVER['REQUEST_URI']).'" method="post">';
        $this->_html .= '<fieldset>';
        $this->_html .= '<legend><img src="'.$this->_path.'logo.gif" alt="" title="" />'.$this->l('Settings').'</legend>';

        $values = Configuration::getInt('LAW_MESSAGE');

        # text
        $this->_html .= '<label>'.$this->l('Text').'</label><div class="margin-form">';
        foreach ($languages as $language) {
            $this->_html .= '<div id="lawtext_'.$language['id_lang'].'" style="display: '.($language['id_lang'] == $defaultLanguage ? 'block' : 'none').';float: left;">';
            $this->_html .= '<input class="translatable" size="75" type="text" id="LAW_MESSAGE_'.$language['id_lang'].'" name="LAW_MESSAGE_'.$language['id_lang'].'" value="'.(isset($values[$language['id_lang']]) ? $values[$language['id_lang']] : '').'" />';
            $this->_html .= '</div>';
        }
        $this->_html .= $this->displayFlags($languages, $defaultLanguage, 'lawtext', 'lawtext', true);
        
        $this->_html .= '</div><div class="clear"></div><script>id_language = Number('.$defaultLanguage.');</script>';

        #page
        $cms_pages = CMS::listCms();
        $this->_html .= '<label>'.$this->l('CMS page for the "more informations" link').'</label><div class="margin-form">';
        $this->_html .= '<select name="COOKIE_LAW_CMS">';

        foreach ($cms_pages as $page) {
            $selected = Configuration::get('COOKIE_LAW_CMS') == $page['id_cms'] ? 'selected="selected"' : '';
            $this->_html .= '<option value="'.$page['id_cms'].'" '.$selected.'>'.$page['meta_title'].'</option>';
        }

        $this->_html .= '</select></div>';

        # background
        $this->_html .= '<label>'.$this->l('Background color').'</label><div class="margin-form">';
        $this->_html .= '<input type="color" name="COOKIE_LAW_BAR_BG" data-hex="true" class="color mColorPickerInput" value="'.Configuration::get('COOKIE_LAW_BAR_BG').'" /></div><div class="clear"></div>';

        # text color
        $this->_html .= '<label>'.$this->l('Text color').'</label><div class="margin-form">';
        $this->_html .= '<input type="color" name="COOKIE_LAW_TEXT_COLOR" data-hex="true" class="color mColorPickerInput" value="'.Configuration::get('COOKIE_LAW_TEXT_COLOR').'" /></div><div class="clear"></div>';

        # opacity
        $this->_html .= '<label>'.$this->l('Bar opacity').'</label><div class="margin-form">';
        $this->_html .= '<input type="text" name="COOKIE_LAW_BAR_OPACITY" value="'.Configuration::get('COOKIE_LAW_BAR_OPACITY').'" />
        <p style="clear:both;" class="preference_description">'.$this->l('from 0.1 to 1').'</p>
        </div><div class="clear"></div>';

        # width
        $this->_html .= '<label>'.$this->l('Bar width').'</label><div class="margin-form">';
        $this->_html .= '<input type="text" name="COOKIE_LAW_BAR_WIDTH" value="'.Configuration::get('COOKIE_LAW_BAR_WIDTH').'" />
        <p style="clear:both;" class="preference_description">'.$this->l('in "%" or "px"').'</p>
        </div><div class="clear"></div>';

        # position
        $this->_html .= '<label>'.$this->l('Bar position').'</label><div class="margin-form">';
        $this->_html .= '<select name="COOKIE_LAW_BAR_POSITION"">
                            <option value="top" '.(Configuration::get('COOKIE_LAW_BAR_POSITION') == 'top' ? 'selected' : '').'>'.$this->l('top of page').'</option>
                            <option value="bottom" '.(Configuration::get('COOKIE_LAW_BAR_POSITION') == 'bottom' ? 'selected' : '').'>'.$this->l('bottom of page').'</option>
                        </select>
                        </div><div class="clear"></div>';

        # padding
        $this->_html .= '<label>'.$this->l('Bar padding').'</label><div class="margin-form">';
        $this->_html .= '<input type="text" name="COOKIE_LAW_BAR_PADDING" value="'.Configuration::get('COOKIE_LAW_BAR_PADDING').'" />
        <p style="clear:both;" class="preference_description">'.$this->l('spacing within the bar, in "px"').'</p>
        </div><div class="clear"></div>';

        # radius
        $this->_html .= '<label>'.$this->l('Bar border radius').'</label><div class="margin-form">';
        $this->_html .= '<input type="text" name="COOKIE_LAW_BAR_RADIUS" value="'.Configuration::get('COOKIE_LAW_BAR_RADIUS').'" />
        <p style="clear:both;" class="preference_description">'.$this->l('bar rounding, in "px", higher value = more rounded').'</p>
        </div><div class="clear"></div>';

        # margins
        $this->_html .= '<label>'.$this->l('Bar margins').'</label><div class="margin-form">';
        $this->_html .= '<input type="text" name="COOKIE_LAW_BAR_MARGIN" value="'.Configuration::get('COOKIE_LAW_BAR_MARGIN').'" />
        <p style="clear:both;" class="preference_description">'.$this->l('margins, in "px": top right bottom left, for eg. 20px 0 20px 0').'</p>
        </div><div class="clear"></div>';

        # text align
        $this->_html .= '<label>'.$this->l('Text align').'</label><div class="margin-form">';
        $this->_html .= '<select name="COOKIE_LAW_TEXT_ALIGN"">
                            <option value="left" '.(Configuration::get('COOKIE_LAW_TEXT_ALIGN') == 'left' ? 'selected' : '').'>'.$this->l('left').'</option>
                            <option value="center" '.(Configuration::get('COOKIE_LAW_TEXT_ALIGN') == 'center' ? 'selected' : '').'>'.$this->l('center').'</option>
                            <option value="right" '.(Configuration::get('COOKIE_LAW_TEXT_ALIGN') == 'right' ? 'selected' : '').'>'.$this->l('right').'</option>
                        </select>
                        </div><div class="clear"></div>';

        # margins
        $this->_html .= '<label>'.$this->l('Z-index (advanced users)').'</label><div class="margin-form">';
        $this->_html .= '<input type="text" name="COOKIE_LAW_BAR_ZINDEX" value="'.Configuration::get('COOKIE_LAW_BAR_ZINDEX').'" />
        <p style="clear:both;" class="preference_description">'.$this->l('z-index for imp_cookies layer, be carefull with modify this setting').'</p>
        </div><div class="clear"></div>';

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

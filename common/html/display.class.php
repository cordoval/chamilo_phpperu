<?php

// $Id: display.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
/**
==============================================================================
 * This is a display library for Chamilo.
 *
 * Include/require it in your code to use its functionality.
 * There are also several display functions in the main api library.
 *
 * All functions static functions inside a class called Display,
 * so you use them like this: e.g.
 * Display :: normal_message($message)
 *
 * @package common.html
==============================================================================
 */
/*
==============================================================================
	   CONSTANTS
==============================================================================
*/
/** the light grey often used in Chamilo*/
define("CHAMILOLIGHTGREY", "#E6E6E6");
/** plain white colour*/
define("HTML_WHITE", "white");
/**
 * Display class
 * contains several functions dealing with the display of
 * table data, messages, help topics, ...
 *
 * @version 1.0.4
 */

class Display
{

    /**
     * Displays a normal message. It is recommended to use this function
     * to display any normal information messages.
     *
     * @author Roan Embrechts
     * @author Tim De Pauw
     * @author Hans De Bisschop
     * @param string $message - include any additional html
     * tags if you need them
     * @param boolean $return
     * @return mixed
     */
    public static function normal_message($message, $return = false)
    {
        $html = array();
        $html[] = '<div class="normal-message">';
        $html[] = $message;
        $html[] = '<div class="close_message" id="closeMessage"></div>';
        $html[] = '</div>';
        
        if ($return)
        {
            return implode("\n", $html);
        }
        
        echo implode("\n", $html);
    }

    /**
     * Displays a message. It is recommended to use this function
     * to display any confirmation or error messages.
     *
     * @author Hugues Peeters
     * @author Roan Embrechts
     * @author Tim De Pauw
     * @author Hans De Bisschop
     * @param string $message - include any additional html
     * tags if you need them
     * @param boolean $return
     * @return mixed
     */
    public static function error_message($message, $return = false)
    {
        $html = array();
        $html[] = '<div class="clear"></div>';
        $html[] = '<div class="error-message">';
        $html[] = $message;
        $html[] = '<div class="close_message" id="closeMessage"></div>';
        $html[] = '</div>';
        
        if ($return)
        {
            return implode("\n", $html);
        }
        
        echo implode("\n", $html);
    }

    public static function error_page($message)
    {
        self :: header();
        self :: error_message($message);
        self :: footer();
        exit();
    }

    /**
     * Displays a message. It is recommended to use this function
     * to display any warning messages.
     *
     * @author Hugues Peeters
     * @author Roan Embrechts
     * @author Tim De Pauw
     * @author Hans De Bisschop
     * @param string $message - include any additional html
     * tags if you need them
     * @param boolean $return
     * @return mixed
     */
    public static function warning_message($message, $return = false)
    {
        $html = array();
        $html[] = '<div class="warning-message">';
        $html[] = $message;
        $html[] = '<div class="close_message" id="closeMessage"></div>';
        $html[] = '</div>';
        
        if ($return)
        {
            return implode("\n", $html);
        }
        
        echo implode("\n", $html);
    }

    /**
     * Return an encrypted mailto hyperlink
     *
     * @param - $email (string) - e-mail
     * @param - $text (string) - clickable text
     * @param - $style_class (string) - optional, class from stylesheet
     * @return - encrypted mailto hyperlink
     */
    public static function encrypted_mailto_link($email, $clickable_text = null, $style_class = '')
    {
        if (is_null($clickable_text))
        {
            $clickable_text = $email;
        }
        //mailto already present?
        if (substr($email, 0, 7) != 'mailto:')
            $email = 'mailto:' . $email;
            
        //class (stylesheet) defined?
        if ($style_class != '')
        {
            $style_class = ' class="full_url_print ' . $style_class . '"';
        }
        else
        {
            $style_class = ' class="full_url_print"';
        }
        
        //encrypt email
        $hmail = '';
        for($i = 0; $i < strlen($email); $i ++)
            $hmail .= '&#' . ord($email{$i}) . ';';
            
        //encrypt clickable text if @ is present
        if (strpos($clickable_text, '@'))
        {
            for($i = 0; $i < strlen($clickable_text); $i ++)
                $hclickable_text .= '&#' . ord($clickable_text{$i}) . ';';
        }
        else
        {
            $hclickable_text = htmlspecialchars($clickable_text);
        }
        
        //return encrypted mailto hyperlink
        return '<a href="' . $hmail . '"' . $style_class . '>' . $hclickable_text . '</a>';
    }

    /**
     * Display the page header
     * @param string $tool_name The name of the page (will be showed in the
     * page title)
     * @param string $help
     */
    public static function header($breadcrumbtrail)
    {
        if (! $breadcrumbtrail instanceof BreadcrumbTrail)
        {
            $breadcrumbtrail = BreadcrumbTrail :: get_instance();
        }
        
        global $language_interface, $adm, $httpHeadXtra, $htmlHeadXtra, $text_dir, $plugins, $interbreadcrumb, $charset, $noPHP_SELF;
        include (Path :: get(SYS_LIB_PATH) . 'html/header.inc.php');
    }

    public static function small_header()
    {
        global $language_interface;
        $document_language = AdminDataManager :: get_instance()->retrieve_language_from_english_name($language_interface)->get_isocode();
        if (empty($document_language))
        {
            //if there was no valid iso-code, use the english one
            $document_language = 'en';
        }
        
        $header = new Header($document_language);
        $header->add_default_headers();
        //        $header->add_javascript_file_header(Path :: get(WEB_PLUGIN_PATH) . 'html_editor/fckeditor/fckeditor.js');
        $header->set_page_title(PlatformSetting :: get('site_name'));
        $header->add_html_header('<style type="text/css">body {background-color:white; padding: 10px;}</style>');
        $header->add_html_header('<script type="text/javascript">var rootWebPath="' . Path :: get(WEB_PATH) . '"</script>');
        $header->display();
        
        echo '<body>' . "\n";
    }

    /**
     * Display the page footer
     */
    public static function footer()
    {
        $footer = new Footer();
        $footer->display();
    }

    public static function small_footer()
    {
        echo '</body>' . "\n";
        echo '</html>' . "\n";
    }

    public static function not_allowed($trail = null, $show_login_form = true)
    {
        if (is_null($trail))
        {
            $trail = BreadcrumbTrail :: get_instance();
        }
        self :: header($trail);
        $home_url = Path :: get(WEB_PATH);
        
        $html[] = Translation :: get('NotAllowed');
        
        if ($show_login_form)
        {
            $html[] = self :: display_login_form();
        }
        
        self :: error_message(implode("\n", $html));
        $_SESSION['request_uri'] = $_SERVER['REQUEST_URI'];
        self :: footer();
        exit();
    }

    static function display_login_form()
    {
        $form = new FormValidator('formLogin', 'post', Path :: get(WEB_PATH) . 'index.php');
        $renderer = & $form->defaultRenderer();
        //$renderer->setElementTemplate('<div>{label}&nbsp;<!-- BEGIN required --><span style="color: #ff0000">*</span><!-- END required --></div><div>{element}</div>');
        $renderer->setElementTemplate('<div class="row">{element}</div>');
        //$renderer->setElementTemplate('<div>{element}</div>','submitAuth');
        $form->setRequiredNote(null);
        $form->addElement('text', 'login', Translation :: get('UserName'), array('size' => 20, 'onclick' => 'this.value=\'\';'));
        $form->addRule('login', Translation :: get('ThisFieldIsRequired'), 'required');
        $form->addElement('password', 'password', Translation :: get('Pass'), array('size' => 20, 'onclick' => 'this.value=\'\';'));
        $form->addRule('password', Translation :: get('ThisFieldIsRequired'), 'required');
        $form->addElement('style_submit_button', 'submitAuth', Translation :: get('Login'), array('class' => 'positive login'));
        $form->setDefaults(array('login' => Translation :: get('Username'), 'password' => '*******'));
        return $form->toHtml();
    }

    public static function tool_title($titleElement)
    {
        if (is_string($titleElement))
        {
            $tit = $titleElement;
            unset($titleElement);
            $titleElement['mainTitle'] = $tit;
        }
        echo '<h3>';
        if ($titleElement['supraTitle'])
        {
            echo '<small>' . $titleElement['supraTitle'] . '</small><br>';
        }
        if ($titleElement['mainTitle'])
        {
            echo $titleElement['mainTitle'];
        }
        if ($titleElement['subTitle'])
        {
            echo '<br /><small>' . $titleElement['subTitle'] . '</small>';
        }
        echo '</h3>';
    }

    public static function get_progress_bar($percent, $step = 2)
    {
        /*
        $html = '<div class="progress_information">';
        $html .= '<div class="progress_bar">';
        for($i = 0; $i < 100; $i += $step)
        {
            if ($percent > $i)
            {
            	$class = 'done';
            }
            else
            {
                $class = '';
            }
            $html .= '<div class="' . $class . '"></div>';
        }
        $html .= '</div>';
        $html .= '<div class="progress_status">' . round($percent, 2) . ' %</div>';
        $html .= '</div>';
        return $html;
        */
        $done = (int) ($percent / $step);
        $rest = (int) (100.0 / $step) - $done;
        return '<div class="progress_information"><div class="progress_bar">' . str_repeat('<div class="done"></div>', $done) . str_repeat('<div class=""></div>', $rest) . '</div><div class="progress_status">' . round($percent, 2) . ' %</div></div>';
    }

    public static function get_rating_bar($percent, $show_text = true, $step = 2)
    {
        /*
        $html = '<div class="rating_information">';
        $html .= '<div class="rating_bar">';
        for($i = 0; $i < 100; $i += $step)
        {
            if ($percent > $i)
            {
                if ($percent <= 50)
                {
                    $class = 'bad';
                }
                elseif ($percent <= 75)
                {
                    $class = 'average';
                }
                else
                {
                    $class = 'good';
                }
            }
            else
            {
                $class = '';
            }
            $html .= '<div class="' . $class . '"></div>';
        }
        $html .= '</div>';
        if ($show_text)
        {
        	$html .= '<div class="rating_status">' . round($percent, 2) . ' %</div>';
        }
        $html .= '</div>';
        return $html;
        */
        $done = (int) ($percent / $step);
        $rest = (int) (100.0 / $step) - $done;
        return '<div class="rating_information"><div class="rating_bar">' . str_repeat('<div class="' . ($percent <= 50 ? 'bad' : $percent <= 75 ? 'average' : 'good') . '"></div>', $done) . str_repeat('<div class=""></div>', $rest) . '</div>' . ($show_text ? '<div class="rating_status">' . round($percent, 2) . ' %</div>' : '') . '</div>';
    }

    static function form_category($title = null, $extra_classes = null)
    {
        $html = array();
        
        if ($title != null)
        {
            $html[] = '<div class="configuration_form' . ($extra_classes ? ' ' . $extra_classes : '') . '" >';
            $html[] = '<span class="category">' . $title . '</span>';
        }
        else
        {
            $html[] = '<div style="clear: both;"></div>';
            $html[] = '</div>';
        }
        
        return implode("\n", $html);
    }

    static function form_row($label = null, $value = null)
    {
        $html = array();
        
        $html[] = '<div class="row">';
        $html[] = '<div class="label">' . $label . '</div>';
        $html[] = '<div class="formw">';
        $html[] = '<div class="element">' . $value . '</div>';
        $html[] = '</div>';
        $html[] = '<div class="clear">&nbsp;</div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }
}
?>
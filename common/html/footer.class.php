<?php
/**
 * $Id: footer.class.php 128 2009-11-09 13:13:20Z vanpouckesven $
 * @package common.html
 */
/**
 * Class to display the footer of a HTML-page
 */
class Footer
{

    /**
     * Create a new Footer
     */
    function Footer()
    {
    }

    function get_setting($variable, $application)
    {
        return PlatformSetting :: get($variable, $application);
    }

    /**
     * Display the footer
     */
    function display()
    {
        echo $this->toHtml();
    }

    /**
     * Returns the HTML code for the footer
     */
    function toHtml()
    {
//        $output[] = '<div class="clear">&nbsp;</div> <!-- "clearing" div to make sure that footer stays below the main and right column sections -->';
//        $output[] = '</div> <!-- end of #main" started at the end of banner.inc.php -->';
//        
//        $show_sitemap = $this->get_setting('show_sitemap', 'menu');
//        
//        if (Authentication :: is_valid() && $show_sitemap == '1')
//        {
//            $output[] = '<div id="sitemap">';
//            $output[] = '<div class="categories">';
//            $udm = UserDataManager :: get_instance();
//            $user = $udm->retrieve_user(Session :: get_user_id());
//            
//            $menumanager = new MenuManager($user);
//            $output[] = $menumanager->render_menu(MenuManager :: ACTION_RENDER_SITEMAP);
//            $output[] = '<div class="clear"></div>';
//            $output[] = '</div>';
//            $output[] = '<div class="clear"></div>';
//            $output[] = '</div>';
//        }
//        
//        $output[] = '<div id="footer"> <!-- start of #footer section -->';
//        $output[] = '<div id="copyright">';
//        $output[] = '<div class="logo">';
//        $output[] = '<a href="http://www.chamilo.org"><img src="' . Theme :: get_common_image_path() . 'logo_footer.png" /></a>';
//        $output[] = '</div>';
//        $output[] = '<div class="links">';
//        
//        $links = array();
//        $links[] = Text :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), time());
//        $links[] = '<a href="' . $this->get_setting('institution_url', 'admin') . '" target="about:blank">' . $this->get_setting('institution', 'admin') . '</a>';
//        
//        if ($this->get_setting('show_administrator_data', 'admin') == 'true')
//        {
//            $admin_data .= Translation :: get('Manager');
//            $admin_data .= ':&nbsp;';
//            $admin_data .= Display :: encrypted_mailto_link($this->get_setting('administrator_email', 'admin'), $this->get_setting('administrator_surname', 'admin') . ' ' . $this->get_setting('administrator_firstname', 'admin'));
//            
//            $links[] = $admin_data;
//        }
//        
//        if ($this->get_setting('show_version_data', 'admin') == '1')
//        {
//            $links[] = Translation :: get('Version') . ' ' . $this->get_setting('version', 'admin');
//        }
//        
//        //        $links[] = Translation :: get('License');
//        //        $links[] = Translation :: get('PrivacyPolicy');
//        //        $links[] = '<a href="http://www.chamilo.org">http://www.chamilo.org</a>';
//        
//
//        $world = PlatformSetting :: get('whoisonlineaccess');
//        
//        if ($world == "1" || $_SESSION['_uid'] && $world == "2")
//        {
//            $links[] = '<a href="' . Path :: get(WEB_PATH) . 'core.php?go=whois_online&application=admin">' . Translation :: get('WhoisOnline') . '</a>';
//        }
//        
//        $links[] = '&copy;&nbsp;' . date('Y');
//        
//        $output[] = implode('&nbsp;|&nbsp;', $links);
//        $output[] = '</div>';
//        $output[] = '<div class="clear"></div>';
//        $output[] = '</div>';
//        
//        $output[] = '   </div> <!-- end of #footer -->';
//        $output[] = '  </div> <!-- end of #outerframe opened in header -->';
//        $output[] = ' </body>';
//        $output[] = '</html>';
//        //$output[] = '<script language="JavaScript" type="text/javascript">( function($) { $(window).unload(function() { alert("ByeNow!"); }); })(jQuery);</script>';
//        return implode("\n", $output);

    	$template = Theme :: get_template();
    	$template->add_filename('footer.tpl');
        
        $show_sitemap = $this->get_setting('show_sitemap', 'menu');
        
        if (Authentication :: is_valid() && $show_sitemap == '1')
        {
            $udm = UserDataManager :: get_instance();
            $user = $udm->retrieve_user(Session :: get_user_id());
            
            $menumanager = new MenuManager($user);
            $menu = $menumanager->render_menu(MenuManager :: ACTION_RENDER_SITEMAP);
            
            $template->assign_block_vars('SITEMAP', array('MENU' => $menu));
        }
        
        $template->assign_var('WEB_PATH' , Path :: get(WEB_PATH) );
        
        $links = array();
        $links[] = Text :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), time());
        $links[] = '<a href="' . $this->get_setting('institution_url', 'admin') . '" target="about:blank">' . $this->get_setting('institution', 'admin') . '</a>';
        
        if ($this->get_setting('show_administrator_data', 'admin') == 'true')
        {
            $admin_data .= Translation :: get('Manager');
            $admin_data .= ':&nbsp;';
            $admin_data .= Display :: encrypted_mailto_link($this->get_setting('administrator_email', 'admin'), $this->get_setting('administrator_surname', 'admin') . ' ' . $this->get_setting('administrator_firstname', 'admin'));
            
            $links[] = $admin_data;
        }
        
        if ($this->get_setting('show_version_data', 'admin') == '1')
        {
            $links[] = Translation :: get('Version') . ' ' . $this->get_setting('version', 'admin');
        }
        
        //        $links[] = Translation :: get('License');
        //        $links[] = Translation :: get('PrivacyPolicy');
        //        $links[] = '<a href="http://www.chamilo.org">http://www.chamilo.org</a>';
        

        $world = PlatformSetting :: get('whoisonlineaccess');
        
        if ($world == "1" || $_SESSION['_uid'] && $world == "2")
        {
            $links[] = '<a href="' . Path :: get(WEB_PATH) . 'core.php?go=whois_online&application=admin">' . Translation :: get('WhoisOnline') . '</a>';
        }
        
        $links[] = '&copy;&nbsp;' . date('Y');
        
        $links = implode('&nbsp;|&nbsp;', $links);
        
        $template->assign_var('LINKS' , $links );
        
        return $template->pparse('footer');
    }
}
?>
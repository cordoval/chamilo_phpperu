<?php

/**
 * $Id: banner.class.php 179 2009-11-12 13:51:39Z vanpouckesven $
 * @package common.html
 */

/**
 * Class to display the banner of a HTML-page
 */
class Banner
{
    private $breadcrumbtrail;

    /**
     * Constructor
     */
    function Banner($breadcrumbtrail)
    {
        $this->breadcrumbtrail = $breadcrumbtrail;
    }

    function get_setting($variable, $application)
    {
        return PlatformSetting :: get($variable, $application);
    }

    /**
     * Displays the banner.
     */
    public function display()
    {
        echo $this->toHtml();
    }

    /**
     * Creates the HTML output for the banner.
     */
    public function toHtml()
    {
        $output = array();
        
        if (Authentication :: is_valid())
        {
            $usermgr = new UserManager($_SESSION['_uid']);
            $user = $usermgr->get_user();
        }
        
        if (! is_null($_SESSION['_as_admin']))
        {
            $output[] = '<div style="width: 100%; height: 20px; text-align: center; background-color: lightblue;">' . Translation :: get('LoggedInAsUser') . ' ' . $user->get_fullname() . ' <a href="index.php?adminuser=1">' . Translation :: get('Back') . '</a></div>';
        }
        
        $output[] = '<a name="top"></a>';
        $output[] = '<div id="header">  <!-- header section start -->';
        $output[] = '<div id="header1"> <!-- top of banner with institution name/hompage link -->';
        $output[] = '<div class="banner"><a href="' . $this->get_path(WEB_PATH) . 'index.php" target="_top"><div class="logo"></div><div class="text">' . $this->get_setting('site_name', 'admin') . '</div></a></div>';
        
        if (Authentication :: is_valid())
        {
            $output[] = '<div class="menu_container">';
            $output[] = '<div class="applications">';
            
            $menumanager = new MenuManager($user);
            $output[] = $menumanager->render_menu('render_mini_bar');
            
            $output[] = '<div class="clear">&nbsp;</div>';
            $output[] = '</div>';
            $output[] = '<div class="clear">&nbsp;</div>';
            $output[] = '</div>';
        }
        
        //$output[] = '<div id="institution">';
        //$output[] = '<a href="'.$this->get_path(WEB_PATH).'index.php" target="_top">'.$this->get_setting('site_name', 'admin').'</a>';
        //$output[] = '-';
        //$output[] = '<a href="'.$this->get_setting('institution_url', 'admin').'" target="_top">'.$this->get_setting('institution', 'admin').'</a>';
        //$output[] = '</div>';
        

        //		$world = PlatformSetting :: get('whoisonlineaccess');
        //
        //		if($world == "1" || $_SESSION['_uid'] && $world == "2")
        //		{
        //
        //			$output[] = '<div style="float:right;">';
        //			$output[] = '<a href="' . $this->get_path(WEB_PATH). 'index_admin.php?go=whois_online">' . Translation :: get('WhoisOnline') . '</a>';
        //			$output[] = '</div>';
        //		}
        

        //not to let the header disappear if there's nothing on the left
        $output[] = '<div class="clear">&nbsp;</div>';
        $output[] = '</div> <!-- end of #header1 -->';
        //		$output[] = '<div id="header2">';
        //
        //		if (isset($_SESSION['_uid']))
        //		{
        //			$usermgr = new UserManager($_SESSION['_uid']);
        //			$user = $usermgr->get_user();
        //
        //			$menumanager = new MenuManager($user);
        //			$output[] = $menumanager->render_menu('render_bar');
        //		}
        //		$output[] = '<div class="clear">&nbsp;</div>';
        //
        //		$output[] = '</div><!-- End of header 2-->';
        

        /*
		-----------------------------------------------------------------------------
			User section
		-----------------------------------------------------------------------------
		*/
        
        $breadcrumbtrail = $this->breadcrumbtrail;
        
        if (! is_null($breadcrumbtrail))
        {
            $output[] = '<div id="trailbox">';
            
            if (! is_null($breadcrumbtrail))
            {
                //$output[] = '<div id="breadcrumbtrail">';
                $output[] = $breadcrumbtrail->render();
                //$output[] = '</div>';
            }
            
            $output[] = '<div class="clear">&nbsp;</div></div>';
        }
        
        // TODO: Check whether we still need anything from the old breadcrumb-generating code
        

        //		global $interbreadcrumb;
        //		if (isset ($nameTools) || is_array($interbreadcrumb))
        //		{
        ////			if (!isset ($_SESSION['_uid']))
        ////			{
        ////				$output[] = " ";
        ////			}
        ////			else
        ////			{
        //				$output[] = '&nbsp;&nbsp;<a href="'.$this->get_path(WEB_PATH).'index.php" target="_top">'.$this->get_setting('site_name', 'admin').'</a>';
        ////			}
        //		}
        //
        //		// else we set the site name bold
        //		if (is_array($interbreadcrumb))
        //		{
        //			foreach ($interbreadcrumb as $breadcrumb_step)
        //			{
        //				$output[] = '&nbsp;&gt; <a href="'.$breadcrumb_step['url'].'" target="_top">'.$breadcrumb_step['name'].'</a>';
        //			}
        //		}
        //
        //		if (isset ($nameTools))
        //		{
        //			if (!isset ($_SESSION['_uid']))
        //			{
        //				$output[] = '&nbsp;';
        //			}
        //			elseif (!defined('CHAMILO_HOMEPAGE') || !CHAMILO_HOMEPAGE)
        //			{
        //				global $noPHP_SELF;
        //				if ($noPHP_SELF)
        //				{
        //					$output[] = '&nbsp;&gt;&nbsp;'.$nameTools;
        //				}
        //				else
        //				{
        //					$output[] = ' &gt; <a href="'.htmlspecialchars($_SERVER['REQUEST_URI']).'" target="_top">'.$nameTools.'</a>';
        //				}
        //			}
        //		}
        

        $output[] = '<div class="clear">&nbsp;</div>';
        $output[] = '</div> <!-- end of the whole #header section -->';
        
        return implode("\n", $output);
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }
}
?>
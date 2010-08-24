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
        	$user = UserDataManager :: get_instance()->retrieve_user($_SESSION['_uid']);
//                    $usermgr = new UserManager($_SESSION['_uid']);
//            $user = $usermgr->get_user();
        }

        if (! is_null($_SESSION['_as_admin']))
        {
            $output[] = '<div style="width: 100%; height: 20px; text-align: center; background-color: lightblue;">' . Translation :: get('LoggedInAsUser') . ' ' . $user->get_fullname() . ' <a href="index.php?adminuser=1">' . Translation :: get('Back') . '</a></div>';
        }

        $output[] = '<a name="top"></a>';
        $output[] = '<div id="header">  <!-- header section start -->';
        $output[] = '<div id="header1"> <!-- top of banner with institution name/hompage link -->';
        $output[] = '<div class="banner"><a href="' . $this->get_path(WEB_PATH) . 'index.php" target="_top"><span class="logo"></span><span class="text">' . $this->get_setting('site_name', 'admin') . '</span></a></div>';

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

        //not to let the header disappear if there's nothing on the left
        $output[] = '<div class="clear">&nbsp;</div>';
        $output[] = '</div> <!-- end of #header1 -->';

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
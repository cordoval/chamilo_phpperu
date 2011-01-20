<?php
namespace install;

use common\libraries\CoreApplication;
use common\libraries\Translation;
use common\libraries\Installer;
use common\libraries\Utilities;
/**
 * $Id: install_manager.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager
 */
require_once dirname(__FILE__) . '/../install_data_manager.class.php';
/**
 * An install manager provides some functionalities to the end user to install
 * his Chamilo platform
 *
 * @author Hans De Bisschop
 */
class InstallManager extends CoreApplication
{
    const APPLICATION_NAME = 'install';
    const DEFAULT_ACTION = self :: ACTION_INSTALL_PLATFORM;

    /**
     * Constant defining an action of the repository manager.
     */
    const ACTION_INSTALL_PLATFORM = 'installer';

    /**
     * Property of this repository manager.
     */

    private $breadcrumbs;

    /**
     * Constructor
     * @param int $user_id The user id of current user
     */
    function __construct()
    {
        parent :: __construct(null);
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    /**
     * Run this repository manager
     */
    function run()
    {
        /*
		 * Only setting breadcrumbs here. Some stuff still calls
		 * forceCurrentUrl(), but that should not affect the breadcrumbs.
		 */
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_INSTALL_PLATFORM :
                $component = $this->create_component('Installer');
                break;
            default :
                $this->set_action(self :: ACTION_INSTALL_PLATFORM);
                $component = $this->create_component('Installer');
        }
        $component->run();
    }

    /**
     * Displays the header.
     * @param array $breadcrumbs Breadcrumbs to show in the header.
     * @param boolean $display_search Should the header include a search form or
     * not?
     */
    function display_header($breadcrumbtrail = null, $display_search = false)
    {
        $this->display_header_content();
    }

    function display_header_content()
    {
        global $chamilo_version;
        $output = array();

        $output[] = '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $output[] = '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">';
        $output[] = '<head>';
        $output[] = '<title>-- ' . $chamilo_version . ' Installation --</title>';
        $output[] = '<link rel="stylesheet" href="../common/libraries/resources/css/aqua/aqua.css" type="text/css"/>';
        $output[] = '<link rel="stylesheet" href="resources/css/aqua/aqua.css" type="text/css"/>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.min.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.dimensions.min.js"></script>';

        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.tabula.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.tablednd.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.ui.min.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.ui.tabs.paging.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.simplemodal.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.treeview.pack.js"></script>';

        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.treeview.async.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.timeout.interval.idle.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.mousewheel.min.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.scrollable.pack.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.xml2json.pack.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.json.js"></script>';

        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.iphone.checkboxes.js"></script>';
        $output[] = '<script type="text/javascript" src="../common/libraries/plugin/jquery/jquery.textarearesizer.js"></script>';


        $output[] = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
        $output[] = '</head>';
        $output[] = '<body dir="' . Translation :: get('TextDirection', null, Utilities :: COMMON_LIBRARIES) . '">';

        $output[] = '<!-- #outerframe container to control some general layout of all pages -->';
        $output[] = '<div id="outerframe">';

        $output[] = '<a name="top"></a>';
        $output[] = '<div id="header">  <!-- header section start -->';
        $output[] = '<div id="header1"> <!-- top of banner with institution name/hompage link -->';
        $output[] = '<div class="banner"><span class="logo"></span><span class="text">Chamilo</span></div>';

        //not to let the header disappear if there's nothing on the left
        $output[] = '<div class="clear">&nbsp;</div>';
        $output[] = '</div> <!-- end of #header1 -->';
        $output[] = '<div class="clear">&nbsp;</div>';
        $output[] = '</div> <!-- end of the whole #header section -->';

        $output[] = '<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->';
        $output[] = '<!--   Begin Of script Output   -->';

        echo implode("\n", $output);
    }

    /**
     * Displays the footer.
     */
    function display_footer()
    {
        $output = array();

        $output[] = '<div class="clear">&nbsp;</div> <!-- "clearing" div to make sure that footer stays below the main and right column sections -->';
        $output[] = '</div> <!-- end of #main" started at the end of banner.inc.php -->';

        $output[] = '<div id="footer"> <!-- start of #footer section -->';
        $output[] = '<div id="copyright">';
        $output[] = '<div class="logo">';
        $output[] = '<a href="http://www.chamilo.org"><img src="' . '../common/libraries/resources/images/aqua/logo_footer.png" /></a>';
        $output[] = '</div>';
        $output[] = '<div class="links">';

        $links = array();
        $links[] = Translation :: get('License');
        $links[] = Translation :: get('PrivacyPolicy');
        $links[] = '<a href="http://www.chamilo.org">http://www.chamilo.org</a>';
        $links[] = '&copy;&nbsp;' . @date('Y');

        $output[] = implode('&nbsp;|&nbsp;', $links);
        $output[] = '</div>';
        $output[] = '<div class="clear"></div>';
        $output[] = '</div>';

        $output[] = '   </div> <!-- end of #footer -->';
        $output[] = '  </div> <!-- end of #outerframe opened in header -->';
        $output[] = ' </body>';
        $output[] = '</html>';

        echo implode("\n", $output);
    }

    /**
     * Helper function for the Application class,
     * pending access to class constants via variables in PHP 5.3
     * e.g. $name = $class :: DEFAULT_ACTION
     *
     * DO NOT USE IN THIS APPLICATION'S CONTEXT
     * Instead use:
     * - self :: DEFAULT_ACTION in the context of this class
     * - YourApplicationManager :: DEFAULT_ACTION in all other application classes
     */
    function get_default_action()
    {
        return self :: DEFAULT_ACTION;
    }
}
?>
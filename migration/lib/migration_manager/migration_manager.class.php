<?php
/**
 * $Id: migration_manager.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.migration_manager
 */

require_once dirname(__FILE__) . '/migration_manager_component.class.php';

/**
 * A migration manager provides some functionalities to the administrator to migrate
 * from an old system to the LCMS
 *
 * @author Sven Vanpoucke
 */
class MigrationManager extends CoreApplication
{
    const APPLICATION_NAME = 'migration';
    
    /**#@+
     * Constant defining an action of the repository manager.
     */
    const ACTION_MIGRATE = 'migrate';

    /**#@-*/
    /**
     * Constructor
     * @param int $user_id The user id of current user
     */
    function MigrationManager($user)
    {
        parent :: __construct($user);
    }

    function get_application_name()
    {
        return self :: APPLICATION_NAME;
    }

    /**
     * Runs the migrationmanager, choose the correct component with the given parameters
     */
    function run()
    {
        $action = $this->get_action();
        $component = null;
        switch ($action)
        {
            case self :: ACTION_MIGRATE :
                $component = MigrationManagerComponent :: factory('Migration', $this);
                break;
            default :
                $this->set_action(self :: ACTION_MIGRATE);
                $component = MigrationManagerComponent :: factory('Migration', $this);
        }
        $component->run();
    }

    /** 
     * Displays the header.
     * @param array $breadcrumbs Breadcrumbs to show in the header.
     * @param boolean $display_search Should the header include a search form or
     * not?
     */
    function display_header($breadcrumbs = array ())
    {
        global $interbreadcrumb;
        if (isset($this->breadcrumbs) && is_array($this->breadcrumbs))
        {
            $breadcrumbs = array_merge($this->breadcrumbs, $breadcrumbs);
        }
        $current_crumb = array_pop($breadcrumbs);
        $interbreadcrumb = $breadcrumbs;
        $title = $current_crumb['name'];
        $title_short = $title;
        if (strlen($title_short) > 53)
        {
            $title_short = substr($title_short, 0, 50) . '&hellip;';
        }
        $this->display_header_content();
    }

    /**
     * Displays the content of the header
     */
    function display_header_content()
    {
        echo '<!DOCTYPE html
		     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
		     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        echo '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">' . "\n";
        echo '<head>' . "\n";
        echo '<title>-- Chamilo Migration --</title>' . "\n";
        echo '<link rel="stylesheet" href="../layout/aqua/css/common.css" type="text/css"/>' . "\n";
        echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . "\n";
        echo '</head>' . "\n";
        echo '<body dir="' . Translation :: get('text_dir') . '">' . "\n";
        
        echo '<!-- #outerframe container to control some general layout of all pages -->' . "\n";
        echo '<div id="outerframe">' . "\n";
        
        echo '<div id="header">  <!-- header section start -->' . "\n";
        echo '<div id="header1"> <!-- top of banner with institution name/hompage link -->' . "\n";
        echo 'Chamilo Migration';
        
        echo '</div>' . "\n";
        echo '<div class="clear">&nbsp;</div>' . "\n";
        echo '</div> <!-- end of the whole #header section -->' . "\n";
        echo '<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->' . "\n";
        echo '<!--   Begin Of script Output   -->' . "\n";
    }

    /**
     * Displays the footer.
     */
    function display_footer()
    {
        echo '</div>';
        echo '<div class="clear">&nbsp;</div> <!-- \'clearing\' div to make sure that footer stays below the main and right column sections -->' . "\n";
        echo "\n";
        echo '<div id="footer"> <!-- start of #footer section -->' . "\n";
        echo $dokeos_version . '&nbsp;&copy;&nbsp;2007-' . date('Y');
        echo '</div> <!-- end of #footer -->' . "\n";
        echo '</div> <!-- end of #outerframe opened in header -->' . "\n";
        echo "\n";
        echo '</body>' . "\n";
        echo '</html>' . "\n";
    }
}
?>
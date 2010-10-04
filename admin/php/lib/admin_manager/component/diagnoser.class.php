<?php
/**
 * $Id: diagnoser.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

/**
 * Weblcms component displays diagnostics about the system
 */
class AdminManagerDiagnoserComponent extends AdminManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (! AdminRights :: is_allowed(AdminRights :: RIGHT_VIEW))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->display_header();
        
        $diag = new Diagnoser($this);
        echo $diag->to_html();
        
        $this->display_footer();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('admin_diagnoser');
    }

}
?>
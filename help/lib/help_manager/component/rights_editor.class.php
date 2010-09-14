<?php
require_once dirname(__FILE__) ."/../../help_rights.class.php";
/**
 * $Id: deleter.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package group.lib.group_manager.component
 */

class HelpManagerRightsEditorComponent extends HelpManager implements AdministrationComponent, DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $help_ids = Request :: get(HelpManager::PARAM_HELP_ITEM);

        if (! is_array($help_ids))
        {
            $help_ids = array($help_ids);
        }

        $locations = array();

        foreach ($help_ids as $help_id)
        {
            $locations[] = HelpRights :: get_location_by_identifier_from_help_subtree($help_id);
        }

        if($this->get_user()->is_platform_admin())
        {
            $manager = new RightsEditorManager($this, $locations);
        
            $manager->exclude_users(array($this->get_user_id()));
            $manager->run();
        }
        else
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
    }
    
    function get_available_rights()
    {
        $array = HelpRights :: get_available_rights();
        return $array;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => HelpManager :: ACTION_BROWSE_HELP_ITEMS)), Translation :: get('HelpManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('help_updater');
    }
    
    function get_additional_parameters()
    {
    	return array(HelpManager :: PARAM_HELP_ITEM);
    }
}
?>
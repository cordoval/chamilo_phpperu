<?php
/**
 * $Id:
 * @package application.lib.search_portal.search_portal_manager
 */

class SearchPortalManagerUserEmailerComponent extends SearchPortalManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(SearchPortalManager :: PARAM_USER);

	    if (!($this->get_user()->is_platform_admin()))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }

        if(!is_array($ids))
        {
        	$ids = array($ids);
        }

        $udm = UserDataManager :: get_instance();

        if (count($ids) > 0)
        {
        	$failures = 0;

			foreach($ids as $id)
			{
	            $users[] = $udm->retrieve_user($id);
			}

			$manager = EmailManager :: construct($this);
			$manager->set_target_users($users);
			$manager->set_parameter(SearchPortalManager :: PARAM_USER, $ids);
			$manager->run();

        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }

    function display_header($trail)
    {
    	$ids = Request :: get(SearchPortalManager :: PARAM_USER);

    	$this->set_parameter(SearchPortalManager :: PARAM_USER, null);

    	$trail = BreadcrumbTrail :: get_instance();
    	$trail->add(new Breadcrumb($this->get_url(array(SearchPortalManager :: PARAM_ACTION => SearchPortalManager :: ACTION_SEARCH)), Translation :: get('SearchPortal')));
        $trail->add(new Breadcrumb($this->get_url(array(SearchPortalManager :: PARAM_USER => $ids)), Translation :: get('Emailer')));
        $trail->add_help('user general');

        return parent :: display_header($trail);
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('search_portal_emailer');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(self :: PARAM_ACTION => self :: ACTION_SEARCH)), Translation :: get('SearchPortalManagerSearcherComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_USER);
    }
}
?>
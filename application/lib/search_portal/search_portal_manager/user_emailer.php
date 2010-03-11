<?php
/**
 * $Id:
 * @package application.lib.search_portal.search_portal_manager
 */

class SearchManagerEmailerComponent extends UserManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(UserManager :: PARAM_USER_USER_ID);
        
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
        
        if (count($ids) > 0)
        {
        	$failures = 0;
        	
			foreach($ids as $id)
			{
	            $users[] = $this->retrieve_user($id);
			}
			
			$manager = new EmailManager($this, $users);
			$manager->set_parameter(UserManager :: PARAM_USER_USER_ID, $ids);
			$manager->run();
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
    function display_header($trail)
    {
    	$ids = Request :: get(UserManager :: PARAM_USER_USER_ID);
        
    	$this->set_parameter(UserManager :: PARAM_USER_USER_ID, null);
    	
    	$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_USER_USER_ID => $ids)), Translation :: get('Emailer')));
        $trail->add_help('user general');
        
        return parent :: display_header($trail);
    }
}
?>
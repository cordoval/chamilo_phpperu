<?php
/**
 * $Id: user_details.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.user.component
 */
require_once dirname(__FILE__) . '/../user_tool.class.php';

class UserToolEmailerComponent extends UserTool
{

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
   		$ids = Request :: get(WeblcmsManager :: PARAM_USERS);
        $udm = UserDataManager :: get_instance();
   		
        if(!is_array($ids))
        {
        	$ids = array($ids);
        } 
        
        if (count($ids) > 0)
        {
        	$failures = 0;
        	
			foreach($ids as $id)
			{
				$users[] = $udm->retrieve_user($id);
			}
			
			$manager = new EmailManager($this, $users);
			$manager->set_parameter(WeblcmsManager :: PARAM_USERS, $ids);
			$manager->run();
        
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
    
	function display_header($trail)
    {
    	$ids = Request :: get(WeblcmsManager :: PARAM_USERS);
        
    	$this->set_parameter(WeblcmsManager :: PARAM_USERS, null);
    	
        $trail = BreadcrumbTrail :: get_instance();
        //$trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => UserTool :: ACTION_UNSUBSCRIBE_USERS)), Translation :: get('UserList')));
        $trail->add(new Breadcrumb($this->get_url(array(WeblcmsManager :: PARAM_USERS => $ids)), Translation :: get('EmailUsers')));
        $trail->add_help('courses user');
        
        return parent :: display_header();
    }

}
?>
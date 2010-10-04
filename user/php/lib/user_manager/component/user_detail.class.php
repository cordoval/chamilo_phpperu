<?php
/**
 * $Id: user_detail.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */
require_once 'HTML/Table.php';

class UserManagerUserDetailComponent extends UserManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$id = Request :: get(UserManager :: PARAM_USER_USER_ID);
		if ($id)
		{
			if (!UserRights :: is_allowed_in_users_subtree(UserRights :: EDIT_RIGHT, $id))
		    {
		      	$this->display_header();
		        Display :: error_message(Translation :: get("NotAllowed"));
		        $this->display_footer();
		        exit();
		    }
	    
			$user = $this->retrieve_user($id);
			$action_bar = $this->get_action_bar($user);
			
			$this->display_header();
			
			echo $action_bar->as_html() . '<br />';
			
			echo $this->display_user_info($user);
			echo '<br />';
			echo $this->display_groups($user);
			echo '<br />';
			
			$apps = WebApplication :: load_all_from_filesystem();
			foreach($apps as $app_name)
			{
				$app = WebApplication :: factory($app_name, $this->get_user());
				$info = $app->get_additional_user_information($user);
				
				if($info)
				{
					echo $info;
					echo '<br />';	
				}
			}
			
			$this->display_additional_information($id);
			
			$this->display_footer();
			
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
		}
	}
	
	/**
	 * Displays the user information
	 *
	 * @param User $user
	 * @return String
	 */
	function display_user_info($user)
	{
		$html = array();
		
		$table = new Html_Table(array('class' => 'data_table'));
		
		$table->setHeaderContents(0, 0, Translation :: get('UserInformation'));
        $table->setCellAttributes(0, 0, array('colspan' => 3, 'style' => 'text-align: center;'));
        
        $table->setCellContents(1, 2, '<img src="' . $user->get_full_picture_url() . '" />');
        $table->setCellAttributes(1, 2, array('rowspan' => 4, 'style' => 'width: 120px; text-align: center;'));
        
        $attributes = array('username', 'firstname', 'lastname', 'official_code', 'email', 'auth_source', 'phone', 'language', 
        					'active', 'activation_date', 'expiration_date', 'registration_date', 'disk_quota', 'database_quota', 'version_quota');
       
        foreach($attributes as $i => $attribute)
        {
        	$table->setCellContents(($i + 1), 0, Translation :: get(Utilities :: underscores_to_camelcase($attribute)));
        	$table->setCellAttributes(($i + 1), 0, array('style' => 'width: 150px;'));
        	
        	$value = $user->get_default_property($attribute);
        	$value = $this->format_property($attribute, $value);
        	
        	$table->setCellContents(($i + 1), 1, $value);

        	if($i >= 4)
        		$table->setCellAttributes(($i + 1), 1, array('colspan' => 2));
        }
		
        $table->altRowAttributes(0, array('class' => 'row_odd'), array('class' => 'row_even'), true);
        
		$html[] = $table->toHtml();
		
		return implode("\n", $html);
	}
	
	function format_property($attribute, $value)
	{
		switch($attribute)
		{
			case 'active':
				return $value ? Translation :: get('True') : Translation :: get('False');
			case 'activation_date':
				return $value == 0 ? Translation :: get('Forever') : DatetimeUtilities :: format_locale_date(null, $value);
			case 'expiration_date':
				return $value == 0 ? Translation :: get('Forever') : DatetimeUtilities :: format_locale_date(null, $value);
			case 'registration_date':
				return DatetimeUtilities :: format_locale_date(null, $value);
			default: return $value;
		}
	}
	
	/**
	 * Displays the user groups
	 *
	 * @param User $user
	 * @return String
	 */
	function display_groups($user)
	{
		$html = array();
		
		$table = new Html_Table(array('class' => 'data_table'));
		
		$table->setHeaderContents(0, 0, Translation :: get('Groups'));
        $table->setCellAttributes(0, 0, array('colspan' => 2, 'style' => 'text-align: center;'));
        
        $table->setHeaderContents(1, 0, Translation :: get('GroupCode'));
        $table->setCellAttributes(1, 0, array('style' => 'width: 150px;'));
        $table->setHeaderContents(1, 1, Translation :: get('GroupName'));
       
        $groups = $user->get_groups();
        
        if(!$groups || $groups->size() == 0)
        {
        	$table->setCellContents(2, 0, Translation :: get('NoGroups'));
        	$table->setCellAttributes(2, 0, array('colspan' => 2, 'style' => 'text-align: center;'));
        }
        else
        {
	        $i = 2;
	   
	        $gm = new GroupManager($this->get_user());
	        
	        while($group = $groups->next_result())
	        {
	        	$url = '<a href="' . $gm->get_link(array(GroupManager :: PARAM_ACTION => GroupManager :: ACTION_VIEW_GROUP, GroupManager :: PARAM_GROUP_ID => $group->get_id())) . '">';
	        	
	        	$table->setCellContents($i, 0, $url . $group->get_code() . '</a>');
	        	$table->setCellAttributes($i, 0, array('style' => 'width: 150px;'));
	        	$table->setCellContents($i, 1, $url . $group->get_name() . '</a>');
				$i++;
	        }
        }

        $table->altRowAttributes(1, array('class' => 'row_odd'), array('class' => 'row_even'), true);
		$html[] = $table->toHtml();
		
		return implode("\n", $html);
	}
	
	function get_action_bar($user)
	{
		$action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path().'action_edit.png', 
					$this->get_user_editing_url($user), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path().'action_delete.png', 
					$this->get_user_delete_url($user), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		$action_bar->add_common_action(new ToolbarItem(Translation :: get('ViewQuota'), Theme :: get_common_image_path().'action_browser.png', 
					$this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_VIEW_QUOTA, 'user_id' => $user->get_id())), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
					
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('VersionQuota'), Theme :: get_common_image_path().'action_statistics.png', 
					$this->get_user_quota_url($user), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('ManageRightsTemplates'), Theme :: get_common_image_path().'action_rights.png', 
					$this->get_manage_user_rights_url($user), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
		
		$action_bar->add_tool_action(new ToolbarItem(Translation :: get('LoginAsUser'), Theme :: get_common_image_path().'action_login.png', 
					$this->get_change_user_url($user), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
					
		return $action_bar;
	}
	
	function display_additional_information($user_id)
	{
		$form_builder = new DynamicFormManager($this, UserManager :: APPLICATION_NAME, 'account_fields', DynamicFormManager :: TYPE_VIEWER);
		$form_builder->set_target_user_id($user_id);
        $form_builder->run();
	}
	
	function get_dynamic_form_title()
	{
		return Translation :: get('AdditionalUserInformation');
	}
	
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(UserManager :: PARAM_ACTION => UserManager :: ACTION_BROWSE_USERS)), Translation :: get('UserManagerAdminUserBrowserComponent')));
    	$breadcrumbtrail->add_help('user_detail');
    }
    
    function get_additional_parameters()
    {
    	return array(UserManager :: PARAM_USER_USER_ID);
    }
}
?>
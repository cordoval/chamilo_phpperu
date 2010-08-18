<?php
/**
 * $Id: user_field_builder.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerUserFieldsBuilderComponent extends UserManager
{
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, DynamicTabsRenderer :: PARAM_SELECTED_TAB => UserManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Users') ));
		$trail->add(new Breadcrumb($this->get_url(array(DynamicFormManager :: PARAM_DYNAMIC_FORM_ACTION => null)), Translation :: get('BuildUserFields')));
		
		if (!UserRights :: is_allowed(UserRights :: VIEW_RIGHT, UserRights :: LOCATION_FIELDS_BUILDER, UserRights :: TYPE_COMPONENT))
        {
            $this->display_header();
            Display :: error_message(Translation :: get("NotAllowed"));
            $this->display_footer();
            exit();
        }
        
		$form_builder = new DynamicFormManager($this, UserManager :: APPLICATION_NAME, 'account_fields', DynamicFormManager :: TYPE_BUILDER);
		$form_builder->run();
	}
}
?>
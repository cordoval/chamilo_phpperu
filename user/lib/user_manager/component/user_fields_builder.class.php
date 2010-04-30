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
		$form_builder = new DynamicFormManager($this, UserManager :: APPLICATION_NAME, 'account_fields', DynamicFormManager :: TYPE_BUILDER);
		$form_builder->run();
	}
	
	function display_header($new_trail)
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER), array(), false, Redirect :: TYPE_CORE), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => UserManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Users') ));
		$trail->add(new Breadcrumb($this->get_url(array(DynamicFormManager :: PARAM_DYNAMIC_FORM_ACTION => null)), Translation :: get('BuildUserFields')));
		
		$trail->merge($new_trail);
		
		return parent :: display_header();
	}
}
?>
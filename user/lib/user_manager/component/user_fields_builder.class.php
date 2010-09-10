<?php
/**
 * $Id: user_field_builder.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerUserFieldsBuilderComponent extends UserManager implements AdministrationComponent
{
	
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
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
	
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('user_fields_builder');
    }
    
}
?>
<?php
/**
 * $Id: additional_account_information.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerAdditionalAccountInformationComponent extends UserManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        Header :: set_section('my_account');
         
    	$form_builder = new DynamicFormManager($this, UserManager :: APPLICATION_NAME, 'account_fields', DynamicFormManager :: TYPE_EXECUTER);
        $form_builder->run();
    }
    
	function display_header($new_trail)
	{
		$trail = BreadcrumbTrail :: get_instance();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyAccount')));
		$trail->merge($new_trail);
		
		parent :: display_header();
		
		$actions[] = 'account';
		$actions[] = 'user_settings';
		$actions[] = 'account_extra';
		
		echo '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        foreach ($actions as $action)
        {
            echo '<li><a';
            if ($action == 'account_extra')
            {
                echo ' class="current"';
            }
            echo ' href="' . $this->get_url(array(UserManager :: PARAM_ACTION => $action)) . '">' . htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($action) . 'Title')) . '</a></li>';
        }
        echo '</ul><div class="tabbed-pane-content"><br />';
	}
	
	function display_footer()
	{
		echo '</div></div>';
		
		parent :: display_footer();
	}
	
	function get_dynamic_form_title()
	{
		return Translation :: get('AdditionalUserInformation');
	}
}
?>
<?php
/**
 * $Id: user_settings.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component
 */

class UserManagerUserSettingsComponent extends UserManagerComponent
{
	const PARAM_APPLICATION = 'category';

	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		Header :: set_section('my_account');

		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('MyAccount')));

		$application = Request :: get(self :: PARAM_APPLICATION);

		if (!$application)
		{
		    $application = AdminManager :: APPLICATION_NAME;
		}

		$form = new ConfigurationForm($application, 'config', 'post', $this->get_url(array(self :: PARAM_APPLICATION => $application)), true);

        if ($form->validate())
        {
            $success = $form->update_user_settings();
            $this->redirect(Translation :: get($success ? 'ConfigurationUpdated' : 'ConfigurationNotUpdated'), ($success ? false : true),
            		array(Application :: PARAM_ACTION => UserManager :: ACTION_USER_SETTINGS, self :: PARAM_APPLICATION => $application));
        }
        else
        {
			$this->display_header($trail);

			if(!$application)
			{
				echo '<div class="normal-message">' . Translation :: get('SelectApplicationToConfigure') . '</div><br />';
			}

			echo $this->get_selecter($this->get_url(), $application);

			$form->display();

			$this->display_footer();
        }
	}

	function display_header($trail)
	{
		parent :: display_header($trail);

		$actions[] = 'account';
		$actions[] = 'user_settings';

		$form_builder = new DynamicFormManager($this, UserManager :: APPLICATION_NAME, 'account_fields', DynamicFormManager :: TYPE_EXECUTER);
        $dynamic_form = $form_builder->get_form();
        if(count($dynamic_form->get_elements()) > 0)
        {
            $actions[] = 'account_extra';
        }

		echo '<div class="tabbed-pane"><ul class="tabbed-pane-tabs">';
        foreach ($actions as $action)
        {
            echo '<li><a';
            if ($action == 'user_settings')
            {
                echo ' class="current"';
            }
            echo ' href="' . $this->get_url(array(UserManager :: PARAM_ACTION => $action)) . '">' . htmlentities(Translation :: get(Utilities :: underscores_to_camelcase($action) . 'Title')) . '</a></li>';
        }
        echo '</ul><div class="tabbed-pane-content">';
	}

	function display_footer()
	{
		$html[] = '<script type="text/javascript">';
        $html[] = '$(document).ready(function() {';
        $html[] = '$(\':checkbox\').iphoneStyle({ checkedLabel: \'' . Translation :: get('On') . '\', uncheckedLabel: \'' . Translation :: get('Off') . '\'});';
        $html[] = '});';
        $html[] = '</script>';
		$html[] = '</div></div>';

		echo implode("\n", $html);

		parent :: display_footer();
	}

	function get_selecter($url, $current_application = null)
    {
        $html = array();

        $html[] = ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/application.js');
        $html[] = '<div class="application_selecter">';

        $the_applications = WebApplication :: load_all();
        $the_applications = array_merge(CoreApplication :: get_list(), $the_applications);

        foreach ($the_applications as $the_application)
        {
            if(!$this->application_has_settings($the_application))
            	continue;

        	if (isset($current_application) && $current_application == $the_application)
            {
                $type = 'application current';
            }
            else
            {
                $type = 'application';
            }

            $application_name = Translation :: get(Utilities :: underscores_to_camelcase($the_application));

            $html[] = '<a href="' . $url . '&' . self :: PARAM_APPLICATION . '=' . $the_application . '">';
            $html[] = '<div class="' . $type . '" style="background-image: url(' . Theme :: get_image_path('admin') . 'place_' . $the_application . '.png);">' . $application_name . '</div>';
            $html[] = '</a>';
        }

        $html[] = '</div>';
        $html[] = '<div style="clear: both;"></div>';

        return implode("\n", $html);
    }

    function application_has_settings($application_name)
    {
    	$conditions[] = new EqualityCondition(Setting :: PROPERTY_APPLICATION, $application_name);
    	$conditions[] = new EqualityCondition(Setting :: PROPERTY_USER_SETTING, 1);
    	$condition = new AndCondition($conditions);

    	return (AdminDataManager :: get_instance()->count_settings($condition) > 0);
    }
}
?>
<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../../forms/variable_form.class.php';

/**
 * Component to edit an existing variable object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerVariableUpdaterComponent extends CdaManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$variable = $this->retrieve_variable(Request :: get(CdaManager :: PARAM_VARIABLE));
		$language_pack_id = $variable->get_language_pack_id();

		$can_edit = CdaRights :: is_allowed(CdaRights :: EDIT_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');

   		if (!$can_edit)
   		{
   		    Display :: not_allowed();
   		}

		$form = new VariableForm(VariableForm :: TYPE_EDIT, $variable, $this->get_url(array(CdaManager :: PARAM_VARIABLE => $variable->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_variable();
			$this->redirect($success ? Translation :: get('VariableUpdated') : Translation :: get('VariableNotUpdated'), !$success,
						    array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_VARIABLES, CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id));
		}
		else
		{
			$this->display_header();
			$form->display();
			$this->display_footer();
		}
	}
	
	function add_additional_breadcrumbs(BreacrumbTrail $breadcrumbtrail)
    {
    	$variable = CdaDataManager :: get_instance()->retrieve_variable(Request :: get(CdaManager :: PARAM_VARIABLE));
		$language_pack_id = $variable->get_language_pack_id();
		
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('CdaManagerAdminLanguagePacksBrowserComponent')));
		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_VARIABLES, CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), Translation :: get('CdaManagerAdminVariablesBrowserComponent')));
    	$breadcrumbtrail->add_help('cda_variable_updater');
    }
    
    function get_additional_parameters()
    {
    	return array(CdaManager :: PARAM_VARIABLE);
    }
}
?>
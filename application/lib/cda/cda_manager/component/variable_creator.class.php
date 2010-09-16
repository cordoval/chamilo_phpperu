<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../../forms/variable_form.class.php';

/**
 * Component to create a new variable object
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerVariableCreatorComponent extends CdaManager implements AdministrationComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$language_pack_id = Request :: get(CdaManager :: PARAM_LANGUAGE_PACK);

	   	$can_add = CdaRights :: is_allowed(CdaRights :: ADD_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');

   		if (!$can_add)
   		{
   		    Display :: not_allowed();
   		}


		$variable = new Variable();
		$variable->set_language_pack_id($language_pack_id);
		$form = new VariableForm(VariableForm :: TYPE_CREATE, $variable, $this->get_url(array(CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id)), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_variable();
			$this->redirect($success ? Translation :: get('VariableCreated') : Translation :: get('VariableNotCreated'), !$success,
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
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_LANGUAGE_PACKS)), Translation :: get('BrowseLanguagePacks')));
		$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_VARIABLES, CdaManager :: PARAM_LANGUAGE_PACK => Request :: get(CdaManager :: PARAM_LANGUAGE_PACK))), Translation :: get('BrowseVariables')));
    	$breadcrumbtrail->add_help('cda_variable_creator');
    }
    
    function get_additional_parameters()
    {
    	return array(CdaManager :: PARAM_LANGUAGE_PACK);
    }
}
?>
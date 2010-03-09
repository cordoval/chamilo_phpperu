<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/cba_form.class.php';

/**
 * Component to edit an existing competency object
 * @author Nick Van Loocke
 */
class CbaManagerEditorCompetencyComponent extends CbaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		//$trail = new BreadcrumbTrail();
		//$trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE)), Translation :: get('BrowseCba')));
		//$trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CBAS)), Translation :: get('BrowseCbas')));
		//$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateCompetency')));

		$competency = $this->retrieve_competency(Request :: get(CbaManager :: PARAM_COMPETENCY));
		$form = new CbaForm(CbaForm :: TYPE_EDITOR_COMPETENCY, $competency, $this->get_url(array(CbaManager :: PARAM_COMPETENCY => $competency->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_competency();
			$this->redirect($success ? Translation :: get('CompetencyUpdated') : Translation :: get('CompetencyNotUpdated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE));
		}
		else
		{
			$this->display_header($trail);
			$form->display();
		}
		$this->display_footer();
	}
	
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}
}
?>
<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/competency_form.class.php';

/**
 * Component to edit an existing competency object
 * @author Nick Van Loocke
 */
class CbaManagerCompetencyEditorComponent extends CbaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('CBA')));
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('BrowseCompetency')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateCompetency')));
		$this->display_header($trail, false, true);

		$competency = $this->retrieve_competency(Request :: get(CbaManager :: PARAM_COMPETENCY));
		$competency_indicator = null;//$this->retrieve_competency_indicator(Request :: get(CbaManager :: PARAM_COMPETENCY_INDICATOR));
		$form = new CompetencyForm(CompetencyForm :: TYPE_EDITOR_COMPETENCY, $competency, $competency_indicator, $this->get_url(array(CbaManager :: PARAM_COMPETENCY => $competency->get_id())), $this->get_user());

		if($form->validate())
		{
			$success_competency = $form->update_competency();
			$success_competency_indicator = 1;//$form->update_competency_indicator();
			if($success_competency == $success_competency_indicator)
				$success = 1;
			$this->redirect($success ? Translation :: get('CompetencyUpdated') : Translation :: get('CompetencyNotUpdated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY));
		}
		else
		{
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
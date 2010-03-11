<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/indicator_form.class.php';

/**
 * Component to edit an existing competency object
 * @author Nick Van Loocke
 */
class CbaManagerIndicatorEditorComponent extends CbaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		//$trail = new BreadcrumbTrail();
		//$trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('BrowseCba')));
		//$trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CBAS)), Translation :: get('BrowseCbas')));
		//$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateCompetency')));

		$indicator = $this->retrieve_indicator(Request :: get(CbaManager :: PARAM_INDICATOR));
		$form = new IndicatorForm(IndicatorForm :: TYPE_EDITOR_INDICATOR, $indicator, $this->get_url(array(CbaManager :: PARAM_INDICATOR => $indicator->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_indicator();
			$this->redirect($success ? Translation :: get('IndicatorUpdated') : Translation :: get('IndicatorNotUpdated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR));
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
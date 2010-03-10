<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/criteria_form.class.php';

/**
 * Component to edit an existing competency object
 * @author Nick Van Loocke
 */
class CbaManagerCriteriaEditorComponent extends CbaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateCriteria')));
		
		
		$criteria = $this->retrieve_criteria(Request :: get(CbaManager :: PARAM_CRITERIA));
		$form = new CriteriaForm(CriteriaForm :: TYPE_EDITOR_CRITERIA, $criteria, $this->get_url(array(CbaManager :: PARAM_CRITERIA => $criteria->get_id())), $this->get_user());

		if($form->validate())
		{
			$success = $form->update_criteria();
			$this->redirect($success ? Translation :: get('CriteriaUpdated') : Translation :: get('CriteriaNotUpdated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA));
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
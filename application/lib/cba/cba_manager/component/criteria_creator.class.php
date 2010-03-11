<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/criteria_form.class.php';
/**
 * 
 * @author Nick Van Loocke
 */
class CbaManagerCriteriaCreatorComponent extends CbaManagerComponent
{

	function run()
	{				
		$criteria = new Criteria();
		$form = new CriteriaForm(CriteriaForm :: TYPE_CREATOR_CRITERIA, $criteria, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_criteria();
			$this->redirect($success ? Translation :: get('CriteriaCreated') : Translation :: get('CriteriaNotCreated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA));
		}
		else
		{
			$new = 'CriteriaCreator';
			$this->display_header($trail, false, true, $new);
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
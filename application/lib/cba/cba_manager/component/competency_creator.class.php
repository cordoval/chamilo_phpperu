<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/competency_form.class.php';
/**
 * 
 * @author Nick Van Loocke
 */
class CbaManagerCompetencyCreatorComponent extends CbaManagerComponent
{

	function run()
	{				
		$competency = new Competency();
		$form = new CompetencyForm(CompetencyForm :: TYPE_CREATOR_COMPETENCY, $competency, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success = $form->create_competency();
			$this->redirect($success ? Translation :: get('CompetencyCreated') : Translation :: get('CompetencyNotCreated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY));
		}
		else
		{
			$new = 'CompetencyCreator';
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
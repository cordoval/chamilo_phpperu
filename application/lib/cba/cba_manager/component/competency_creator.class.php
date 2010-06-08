<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../../forms/competency_form.class.php';
/**
 * 
 * @author Nick Van Loocke
 */
class CbaManagerCompetencyCreatorComponent extends CbaManager
{

	function run()
	{			
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('CBA')));
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('BrowseCompetency')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateCompetency')));
		$this->display_header($trail, false, true);
		
		$competency = new Competency();
		$competency_indicator = new CompetencyIndicator();
		$competency->set_owner_id($this->get_user_id());
		$form = new CompetencyForm(CompetencyForm :: TYPE_CREATOR_COMPETENCY, $competency, $competency_indicator, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success_competency = $form->create_competency();
			$success_competency_indicator = $form->create_competency_indicator();
			if($success_competency == $success_competency_indicator)
				$success = 1;
				$new_category_id = $form->exportValue(Competency :: PROPERTY_PARENT_ID);
			$this->redirect($success ? Translation :: get('CompetencyCreated') : Translation :: get('CompetencyNotCreated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY, 'category' => $new_category_id));
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
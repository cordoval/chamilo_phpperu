<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../../forms/criteria_form.class.php';
/**
 * 
 * @author Nick Van Loocke
 */
class CbaManagerCriteriaCreatorComponent extends CbaManager
{

	function run()
	{	
		$trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY)), Translation :: get('CBA')));
        $trail->add(new Breadcrumb($this->get_url(array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA)), Translation :: get('BrowseCriteria')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('CreateCriteria')));
		$this->display_header($trail, false, true);
		
		$criteria = new Criteria();
		$criteria_score = new CriteriaScore();
		$criteria->set_owner_id($this->get_user_id());
		$form = new CriteriaForm(CriteriaForm :: TYPE_CREATOR_CRITERIA, $criteria, $criteria_score, $this->get_url(), $this->get_user());

		if($form->validate())
		{
			$success_criteria = $form->create_criteria();
			$success_criteria_score = $form->create_criteria_score();
			if($success_criteria == $success_criteria_score)
				$success = 1;
				$new_category_id = $form->exportValue(Criteria :: PROPERTY_PARENT_ID);
			$this->redirect($success ? Translation :: get('CriteriaCreated') : Translation :: get('CriteriaNotCreated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA, 'category' => $new_category_id));
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
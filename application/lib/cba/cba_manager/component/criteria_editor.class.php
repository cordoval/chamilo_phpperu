<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__).'/../../forms/criteria_form.class.php';

require_once dirname(__FILE__).'/../../criteria_score.class.php';

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
		$ids = Request :: get(CbaManager :: PARAM_CRITERIA);
        if (! empty($ids))
        {
	        if (!is_array($ids))
	        {
	        	$ids = array($ids);
	        }
        }
		
		$trail = new BreadcrumbTrail();
		$trail->add(new Breadcrumb($this->get_url(), Translation :: get('UpdateCriteria')));
		
		$id = Request :: get(CbaManager :: PARAM_CRITERIA);
		$criteria = $this->retrieve_criteria($id);		
		$condition = new EqualityCondition(CriteriaScore :: PROPERTY_CRITERIA_ID, $id);
		$count_links = $this->count_criterias_score($condition);
				
		for($i = 0; $i < $count_links; $i++)
		{
			if($i == 0)
			{	
				$criteria_score0 = $this->retrieve_criteria_score($id);
			}
			$id_new = $criteria_score->get_id();
			$id_new++;

			$criteria_score1 = $this->retrieve_criteria_score_new($id, $id_new);
			dump($criteria_score);
		}
		
		exit();
		
		$form = new CriteriaForm(CriteriaForm :: TYPE_EDITOR_CRITERIA, $criteria, $criteria_score, $this->get_url(array(CbaManager :: PARAM_CRITERIA => $criteria->get_id())), $this->get_user());

		if($form->validate())
		{
			$success_criteria = $form->update_criteria();
			$success_criteria_score = $form->update_criteria_score();
			if($success_criteria == $success_criteria_score)
				$success = 1;
			
			foreach ($ids as $id)
            {
            	$criteria = $this->retrieve_criteria($id);
            	$new_category_id = $this->move_criterias_to_category($form, $ids, $criteria);	
            }
				
			$this->redirect($success ? Translation :: get('CriteriaUpdated') : Translation :: get('CriteriaNotUpdated'), !$success, array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA, 'category' => $new_category_id));  
		}
		else
		{
			$this->display_header($trail);
			$form->display();
		}
		$this->display_footer();
	}
	
    function move_criterias_to_category($form, $ids, $criteria)
    {    	
        $category = $form->exportValue(Criteria :: PROPERTY_PARENT_ID);
        if (! is_array($ids))
            $ids = array($ids);
        
        $condition = new InCondition(Criteria :: PROPERTY_ID, $ids);
        $cdm = CbaDataManager :: get_instance()->retrieve_criterias($condition);        
        $criteria->move($category);

        return $category;
    }
	
	function display_footer()
	{
		echo '</div>';
		echo '<div class="clear">&nbsp;</div>';
		Display :: footer();
	}
}
?>
<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__) . '/../../criteria_score.class.php';

/**
 * Component to delete criteria objects
 * @author Nick Van Loocke
 */
class CbaManagerCriteriaDeleterComponent extends CbaManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[CbaManager :: PARAM_CRITERIA];
		$failures = 0;
		
		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$cba = $this->retrieve_criteria($id);
				$category_id = $cba->get_parent_id();
				
				$condition = new EqualityCondition(CriteriaScore :: PROPERTY_CRITERIA_ID, $id);
				$count_scores = $this->count_criterias_score($condition);

				for($i = 0; $i < $count_scores; $i++)
				{
					$cba_score = $this->retrieve_criteria_score($id);	
					$cba_score->delete();		
				}
				
				if (!$cba->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCriteriaNotDeleted';
				}
				else
				{
					$message = 'SelectedCriteriasNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCriteriaDeleted';
				}
				else
				{
					$message = 'SelectedCriteriasDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_CRITERIA, 'category' => $category_id));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoCriteriasSelected')));
		}
		
		
	}
}
?>
<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';
require_once dirname(__FILE__) . '/../../competency_indicator.class.php';

/**
 * Component to delete competency objects + competency_indicator objects
 * @author Nick Van Loocke
 */
class CbaManagerCompetencyDeleterComponent extends CbaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[CbaManager :: PARAM_COMPETENCY];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$cba = $this->retrieve_competency($id);
				$category_id = $cba->get_parent_id();
				$cba->delete();
				$cba_competency_indicator = $this->retrieve_competency_indicator($id);
				$cba_competency_indicator->delete();					

				if (!$cba->delete() || !$cba_competency_indicator->delete())
				{
					$failures++;
				}							
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCompetencyNotDeleted';
				}
				else
				{
					$message = 'SelectedCompetencysNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedCompetencyDeleted';
				}
				else
				{
					$message = 'SelectedCompetencysDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY, 'category' => $category_id));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoCompetencysSelected')));
		}
	}
}
?>
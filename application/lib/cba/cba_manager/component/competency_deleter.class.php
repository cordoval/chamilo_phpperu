<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';

/**
 * Component to delete competency objects
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

				if (!$cba->delete())
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

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_COMPETENCY));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoCompetencysSelected')));
		}
	}
}
?>
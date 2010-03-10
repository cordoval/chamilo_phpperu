<?php
require_once dirname(__FILE__).'/../cba_manager.class.php';
require_once dirname(__FILE__).'/../cba_manager_component.class.php';

/**
 * Component to delete indicator objects
 * @author Nick Van Loocke
 */
class CbaManagerIndicatorDeleterComponent extends CbaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[CbaManager :: PARAM_INDICATOR];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$cba = $this->retrieve_indicator($id);

				if (!$cba->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedIndicatorNotDeleted';
				}
				else
				{
					$message = 'SelectedIndicatorsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedIndicatorDeleted';
				}
				else
				{
					$message = 'SelectedIndicatorsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CbaManager :: PARAM_ACTION => CbaManager :: ACTION_BROWSE_INDICATOR));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoIndicatorsSelected')));
		}
	}
}
?>
<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';

/**
 * Component to delete variables objects
 * @author Sven Vanpoucke
 * @author 
 */
class CdaManagerVariableDeleterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[CdaManager :: PARAM_VARIABLE];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$variable = $this->retrieve_variable($id);

				if (!$variable->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariableDeleted';
				}
				else
				{
					$message = 'SelectedVariableDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariablesDeleted';
				}
				else
				{
					$message = 'SelectedVariablesDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_BROWSE_VARIABLES));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoVariablesSelected')));
		}
	}
}
?>
<?php
/**
 * @package application.cda.cda.component
 */
require_once dirname(__FILE__).'/../cda_manager.class.php';
require_once dirname(__FILE__).'/../cda_manager_component.class.php';

/**
 * Component to delete variables objects
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class CdaManagerVariableDeleterComponent extends CdaManagerComponent
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$can_delete = CdaRights :: is_allowed(CdaRights :: DELETE_RIGHT, CdaRights :: LOCATION_VARIABLES, 'manager');

   		if (!$can_delete)
   		{
   		    Display :: not_allowed();
   		}

		$ids = $_GET[CdaManager :: PARAM_VARIABLE];
		$failures = 0;
		$language_pack_id = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$variable = $this->retrieve_variable($id);

				if(!$language_pack_id)
					$language_pack_id = $variable->get_language_pack_id();

				if (!$variable->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariableNotDeleted';
				}
				else
				{
					$message = 'SelectedVariablesNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedVariableDeleted';
				}
				else
				{
					$message = 'SelectedVariablesDeleted';
				}
			}

			$this->redirect($message, $failures,
						    array(CdaManager :: PARAM_ACTION => CdaManager :: ACTION_ADMIN_BROWSE_VARIABLES, CdaManager :: PARAM_LANGUAGE_PACK => $language_pack_id));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoVariablesSelected')));
		}
	}
}
?>
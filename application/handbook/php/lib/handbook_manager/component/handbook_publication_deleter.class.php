<?php
/**
 * @package application.handbook.handbook.component
 */
require_once dirname(__FILE__).'/../handbook_manager.class.php';

/**
 * Component to delete handbook_publications objects
 * @author Sven Vanpoucke
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookPublicationDeleterComponent extends HandbookManager
{
	/**
	 * Runs this component and displays its output.
	 */
	function run()
	{
		$ids = $_GET[HandbookManager :: PARAM_HANDBOOK_PUBLICATION];
		$failures = 0;

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $id)
			{
				$handbook_publication = $this->retrieve_handbook_publication($id);

				if (!$handbook_publication->delete())
				{
					$failures++;
				}
			}

			if ($failures)
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedHandbookPublicationNotDeleted';
				}
				else
				{
					$message = 'Selected{HandbookPublicationsNotDeleted';
				}
			}
			else
			{
				if (count($ids) == 1)
				{
					$message = 'SelectedHandbookPublicationDeleted';
				}
				else
				{
					$message = 'SelectedHandbookPublicationsDeleted';
				}
			}

			$this->redirect(Translation :: get($message), ($failures ? true : false), array(HandbookManager :: PARAM_ACTION => HandbookManager :: ACTION_BROWSE_HANDBOOK_PUBLICATIONS));
		}
		else
		{
			$this->display_error_page(htmlentities(Translation :: get('NoHandbookPublicationsSelected')));
		}
	}
}
?>
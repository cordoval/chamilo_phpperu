<?php
namespace application\handbook;
use common\libraries\Translation;
use common\libraries\Utilities;

require_once dirname(__FILE__).'/../handbook_manager.class.php';

/**
 * Component to delete handbook_publications objects
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
                $fail_message = '';

		if (!empty ($ids))
		{
			if (!is_array($ids))
			{
				$ids = array ($ids);
			}

			foreach ($ids as $handbook_publication_id)
			{
                                //delete publication
				$handbook_publication = $this->retrieve_handbook_publication($handbook_publication_id);

                                if (!$handbook_publication->delete())
				{
					$failures++;
                                        $fail_message .= 'SelectedHandbookPublicationNotDeleted_';
				}
                                //delete location
                                $location = HandbookRights::get_location_by_identifier_from_handbooks_subtree($handbook_publication_id);
                                if($location)
                                {
                                    if(!$location->remove())
                                    {
                                        $failures++;
                                        $fail_message .= 'SelectedHandbookPublicationLocationNotDeleted';
				
                                    }
                                }


                                //TODO: delete preferences
			}

			if ($failures)
			{
				
					$message = $fail_message;
				
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
			$this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', array('OBJECT' => Translation::get('HandbookPublications')), Utilities::COMMON_LIBRARIES)));
		}
	}
}
?>
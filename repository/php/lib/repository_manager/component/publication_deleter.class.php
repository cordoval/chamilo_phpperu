<?php
namespace repository;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Application;
use common\libraries\Utilities;
/**
 * $Id: publication_deleter.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component
 */
/**
 * Repository manager component which provides functionality to delete a
 * learning object publication from the publication overview.
 */
class RepositoryManagerPublicationDeleterComponent extends RepositoryManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(RepositoryManager :: PARAM_PUBLICATION_ID);
        $application = Request :: get(RepositoryManager :: PARAM_PUBLICATION_APPLICATION);

        if (! empty($id) && !empty($application))
        {
            $succes = RepositoryDataManager :: delete_content_object_publication($application, $id);

            if ($succes)
            {
             	$message =  Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('Publication')), Utilities :: COMMON_LIBRARIES);
            }
            else
            {
                $message = Translation :: get('ObjectNotDeleted', array('OBJECT' => Translation :: get('Publication')), Utilities :: COMMON_LIBRARIES);
            }

            $this->redirect($message, !$succes, array(Application :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_MY_PUBLICATIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('ContentObject')), Utilities :: COMMON_LIBRARIES)));
        }
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_VIEW_MY_PUBLICATIONS)), Translation :: get('RepositoryManagerPublicationBrowserComponent')));
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(RepositoryManager :: PARAM_ACTION => RepositoryManager :: ACTION_BROWSE_CONTENT_OBJECTS)), Translation :: get('RepositoryManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('repository_publication_deleter');
    }

    function get_additional_parameters()
    {
    	return array(RepositoryManager :: PARAM_PUBLICATION_APPLICATION, RepositoryManager :: PARAM_PUBLICATION_ID);
    }
}
?>
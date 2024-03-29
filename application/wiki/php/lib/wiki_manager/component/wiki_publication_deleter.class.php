<?php

namespace application\wiki;

use common\libraries\WebApplication;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\Utilities;
use repository\RepoViewer;
use application\gradebook\GradebookManager;
use application\gradebook\GradebookUtilities;

/**
 * $Id: wiki_publication_deleter.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */

/**
 * Component to delete wiki_publications objects
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationDeleterComponent extends WikiManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = $_GET[WikiManager :: PARAM_WIKI_PUBLICATION];
        $failures = 0;

        if (!empty($ids))
        {
            if (!is_array($ids))
            {
                $ids = array($ids);
            }

//            if (retrieve_evaluation_ids_by_publication($this->get_parent()->APPLICATION_NAME))
//            {
//            	$button = '<a ' . $id . $class . 'href="' . htmlentities($elmt['href']) . '" title="' . $label . '"' . ($elmt['confirm'] ? ' onclick="javascript: return confirm(\'' . addslashes(htmlentities(Translation :: get('ConfirmYourChoice'), ENT_QUOTES, 'UTF-8')) . '\');"' : '') . '>' . $button . '</a>';
//            }

            foreach ($ids as $id)
            {
                $wiki_publication = $this->retrieve_wiki_publication($id);
                if (WebApplication :: is_active('gradebook'))
                {
                    //require_once WebApplication :: get_application_class_lib_path(GradebookManager :: APPLICATION_NAME) . 'gradebook_utilities.class.php';
                    if (!GradebookUtilities :: move_internal_item_to_external_item(WikiManager :: APPLICATION_NAME, $wiki_publication->get_id()))
                        $message = 'failed to move internal evaluation to external evaluation';
                }

                if (!$wiki_publication->delete())
                {
                    $failures++;
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectNotDeleted', array('OBJECT' => Translation :: get('WikiPublication')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsNotDeleted', array('OBJECT' => Translation :: get('WikiPublications')), Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectDeleted', array('OBJECT' => Translation :: get('WikiPublication')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsDeleted', array('OBJECT' => Translation :: get('WikiPublications')), Utilities :: COMMON_LIBRARIES);
                }
            }

            $this->redirect($message, ($failures ? true : false), array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('wiki_publications_browser');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('WikiManagerWikiPublicationsBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_WIKI_PUBLICATION);
    }

}

?>
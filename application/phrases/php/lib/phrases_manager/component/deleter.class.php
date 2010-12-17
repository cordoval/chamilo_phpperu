<?php
namespace application\phrases;

use common\libraries\Request;
use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Utilities;
use application\gradebook\GradebookUtilities;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerDeleterComponent extends PhrasesManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $ids = Request :: get(PhrasesManager :: PARAM_PHRASES_PUBLICATION);
        $failures = 0;

        if (! empty($ids))
        {
            if (! is_array($ids))
            {
                $ids = array($ids);
            }

            foreach ($ids as $id)
            {
                $phrases_publication = $this->retrieve_phrases_publication($id);
                if (! $phrases_publication->is_visible_for_target_user($this->get_user()))
                {
                    $failures ++;
                }
                else
                {
                    if (WebApplication :: is_active('gradebook'))
                    {
                        if (! GradebookUtilities :: move_internal_item_to_external_item(PhrasesManager :: APPLICATION_NAME, $id))
                            $message = 'failed to move internal evaluation to external evaluation';
                    }
                    if (! $phrases_publication->delete())
                    {
                        $failures ++;
                    }
                }
            }

            if ($failures)
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectDeleted', array(
                            'OBJECT' => Translation :: get('PhrasesPublication')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsDeleted', array(
                            'OBJECTS' => Translation :: get('PhrasesPublications')), Utilities :: COMMON_LIBRARIES);
                }
            }
            else
            {
                if (count($ids) == 1)
                {
                    $message = Translation :: get('ObjectDeleted', array(
                            'OBJECT' => Translation :: get('PhrasesPublication')), Utilities :: COMMON_LIBRARIES);
                }
                else
                {
                    $message = Translation :: get('ObjectsDeleted', array(
                            'OBJECTS' => Translation :: get('PhrasesPublications')), Utilities :: COMMON_LIBRARIES);
                }
            }

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(
                    PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES)));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_deleter');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION);
    }
}
?>
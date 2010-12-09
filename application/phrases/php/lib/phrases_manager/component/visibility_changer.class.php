<?php
namespace application\phrases;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Utilities;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesManagerVisibilityChangerComponent extends PhrasesManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $pid = Request :: get(self :: PARAM_PHRASES_PUBLICATION);

        if ($pid)
        {
            $publication = $this->retrieve_phrases_publication($pid);

            if (! $publication->is_visible_for_target_user($this->get_user()))
            {
                $this->redirect(null, false, array(
                        PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS));
            }

            $publication->toggle_visibility();
            $succes = $publication->update();

            $message = $succes ? 'VisibilityChanged' : 'VisibilityNotChanged';

            $this->redirect(Translation :: get($message), ! $succes, array(
                    PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS));
        }
        else
        {
            $this->redirect(Translation :: get('NoObjectsSelected', null, Utilities :: COMMON_LIBRARIES), true, array(
                    PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS));
        }
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('phrases_visibility_changer');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PhrasesManager :: PARAM_ACTION => PhrasesManager :: ACTION_BROWSE_PHRASES_PUBLICATIONS)), Translation :: get('PhrasesManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PHRASES_PUBLICATION);
    }
}
?>
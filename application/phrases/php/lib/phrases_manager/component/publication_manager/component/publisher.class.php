<?php
namespace application\phrases;

use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Application;
use common\libraries\Utilities;

use repository\content_object\assessment\Assessment;

use common\extensions\repo_viewer\RepoViewerInterface;
use common\extensions\repo_viewer\RepoViewer;
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
class PhrasesPublicationManagerPublisherComponent extends PhrasesPublicationManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_PHRASES)), Translation :: get('Phrases')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish', null, Utilities::COMMON_LIBRARIES)));
        $trail->add_help('phrases general');

        

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new PhrasesPublisher($this);
            $publisher->get_publications_form(RepoViewer::get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Assessment :: get_type_name());
    }
}
?>
<?php

namespace application\alexia;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Application;
use common\libraries\Translation;
use common\extensions\repo_viewer\RepoViewer;
use common\extensions\repo_viewer\RepoViewerInterface;

use repository\content_object\link\Link;
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.alexia.alexia_manager.component
 */
class AlexiaManagerPublisherComponent extends AlexiaManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Alexia')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish', null ,Utilities::COMMON_LIBRARIES)));
        $trail->add_help('alexia general');

        

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new AlexiaPublisher($this);
            $publisher->get_publications_form(RepoViewer::get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Link :: get_type_name());
    }
}
?>
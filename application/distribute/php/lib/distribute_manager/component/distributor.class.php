<?php

namespace application\distribute;

use common\libraries\WebApplication;
use common\extensions\repo_viewer\RepoViewerInterface;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Application;
use common\libraries\Translation;
use common\extensions\repo_viewer\RepoViewer;
use repository\content_object\announcement\Announcement;

/**
 * $Id: distributor.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.distribute.distribute_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('distribute') . 'distributor/announcement_distributor.class.php';

class DistributeManagerDistributorComponent extends DistributeManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => DistributeManager :: ACTION_BROWSE_ANNOUNCEMENT_DISTRIBUTIONS)), Translation :: get('Distribute')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Compose')));
        $trail->add_help('distribute general');

        

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new AnnouncementDistributor($this);
            $publisher->get_publications_form(RepoViewer::get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Announcement :: get_type_name());
    }
}
?>
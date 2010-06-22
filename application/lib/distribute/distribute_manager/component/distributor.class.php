<?php
/**
 * $Id: distributor.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.lib.distribute.distribute_manager.component
 */
require_once dirname(__FILE__) . '/../distribute_manager.class.php';
require_once Path :: get_application_path() . 'lib/distribute/distributor/announcement_distributor.class.php';

class DistributeManagerDistributorComponent extends DistributeManager
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

        $repo_viewer = new RepoViewer($this, Announcement :: get_type_name());

        if (!$repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $publisher = new AnnouncementDistributor($this);
            $publisher->get_publications_form($repo_viewer->get_selected_objects());
        }
    }
}
?>
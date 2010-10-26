<?php
namespace application\weblcms;


/**
 * $Id: announcement_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.announcement.component
 */
require_once dirname(__FILE__) . '/../../publisher/content_object_publisher.class.php';

class ToolComponentPublisherComponent extends ToolComponent implements RepoViewerInterface
{

    function run()
    {
        if (!$this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        //$trail = BreadcrumbTrail :: get_instance();

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $object = RepoViewer::get_selected_objects();
            $publisher = new ContentObjectPublisher($this);
            $publisher->get_publications_form($object);
        }
    }

//    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
//    {
//        $tool = preg_replace('/Tool.*/',"",get_class($this->get_parent()));
//
//        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get($tool.'ToolBrowserComponent')));
//    }
//
//    function get_additional_parameters()
//    {
//        return array(RepoViewer::PARAM_ID, RepoViewer::PARAM_ACTION);
//    }
}

?>
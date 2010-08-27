<?php
/**
 * $Id: announcement_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.announcement.component
 */
require_once dirname(__FILE__) . '/../../publisher/content_object_publisher.class.php';

class ToolPublisherComponent extends ToolComponent
{

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));

        $repo_viewer = new RepoViewer($this, $this->get_parent()->get_allowed_types());

        if (!$repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $object = $repo_viewer->get_selected_objects();
            $publisher = new ContentObjectPublisher($this);
            $publisher->get_publications_form($object);
        }
    }
}
?>
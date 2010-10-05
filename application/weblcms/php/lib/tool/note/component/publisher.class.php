<?php

/**
 * $Id: note_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.note.component
 */
class NoteToolPublisherComponent extends NoteTool
{

    function run()
    {
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('LinkToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(RepoViewer::PARAM_ID, RepoViewer::PARAM_ACTION);
    }

}

?>
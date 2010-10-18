<?php
/**
 * $Id: document_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */

class DocumentToolPublisherComponent extends DocumentTool
{

    function run()
    {
        //TODO: change this to real roles and rights
        $category = $this->get_category(Request :: get(WeblcmsManager :: PARAM_CATEGORY));
        if ($category && $category->get_name() == 'Dropbox')
        {
            $this->get_parent()->set_right(ADD_RIGHT, true);
        }
        
        ToolComponent :: launch($this);
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_BROWSE)), Translation :: get('DocumentToolBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(RepoViewer::PARAM_ID, RepoViewer::PARAM_ACTION);
    }
}
?>
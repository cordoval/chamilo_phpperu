<?php
/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.blog.component
 */
require_once dirname(__FILE__) . '/../../blog.class.php';

class BlogDisplayCreatorComponent extends BlogDisplay
{
    function run()
    {
        ComplexDisplayComponent :: launch($this);
    }

    function  add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ComplexDisplay::PARAM_DISPLAY_ACTION => ComplexDisplay :: ACTION_VIEW_COMPLEX_CONTENT_OBJECT)), $this->get_root_content_object()->get_title()));
    }
}

?>
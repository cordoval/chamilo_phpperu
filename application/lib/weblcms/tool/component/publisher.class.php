<?php
/**
 * $Id: announcement_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.announcement.component
 */
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class ToolPublisherComponent extends ToolComponent
{

    function run()
    {
        if (! $this->is_allowed(ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $pub = new RepoViewer($this, $this->get_parent()->get_allowed_types());
        
        if (!$pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $object = $pub->get_selected_objects();
            $publisher = new ContentObjectPublisher($pub);
            $html[] = $publisher->get_publications_form($object);
        }
        
        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }
}
?>
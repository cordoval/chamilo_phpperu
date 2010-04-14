<?php
/**
 * $Id: note_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.note.component
 */
require_once dirname(__FILE__) . '/../note_tool.class.php';
require_once dirname(__FILE__) . '/../note_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class NoteToolPublisherComponent extends NoteToolComponent
{

    function run()
    {
        if (! $this->is_allowed(ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => 'publish')), Translation :: get('Publish')));
        $trail->add_help('courses note tool');
        
        $pub = new ContentObjectRepoViewer($this, 'note', true);
        
        if (!$pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new ContentObjectPublisher($pub);
            $html[] = $publisher->get_publications_form($pub->get_selected_objects());
        }
        
        $this->display_header($trail, true);
        echo implode("\n", $html);
        $this->display_footer();
    }
}
?>
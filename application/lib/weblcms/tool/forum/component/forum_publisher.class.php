<?php
/**
 * $Id: forum_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.forum.component
 */
require_once dirname(__FILE__) . '/../forum_tool.class.php';
require_once dirname(__FILE__) . '/../forum_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class ForumToolPublisherComponent extends ForumToolComponent implements RepoViewerInterface
{

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            Display :: not_allowed();
            exit();
        }
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses forum tool');

        $pub = ContentObjectRepoViewer :: construct($this);

        if (! $pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            //$html[] = 'ContentObject: ';
            $publisher = new ContentObjectPublisher($pub);
            $html[] = $publisher->get_publications_form($pub->get_selected_objects());
        }

        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }

    public function get_allowed_content_object_types()
    {
        return array(Forum :: get_type_name());
    }
}
?>
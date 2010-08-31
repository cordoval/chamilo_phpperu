<?php
/**
 * $Id: link_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.link.component
 */
require_once dirname(__FILE__) . '/../link_tool.class.php';
require_once dirname(__FILE__) . '/../link_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class LinkToolPublisherComponent extends LinkToolComponent implements RepoViewerInterface
{

    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('courses link tool');

        if (Request :: get('pcattree') != null)
        {
            foreach (Tool :: get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
            {
                if ($breadcrumb)
                    $trail->add(new Breadcrumb($this->get_url(), $breadcrumb->get_name()));
            }
        }
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), Translation :: get('Publish')));
        $pub = new ContentObjectRepoViewer($this);

        if (! $pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new ContentObjectPublisher($pub);
            $html[] = $publisher->get_publications_form($pub->get_selected_objects());
        }

        $this->display_header();
        echo implode("\n", $html);
        $this->display_footer();
    }

    public function get_allowed_content_object_types()
    {
        return array(Link :: get_type_name());
    }
}
?>
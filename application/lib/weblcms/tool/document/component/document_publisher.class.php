<?php
/**
 * $Id: document_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */
require_once dirname(__FILE__) . '/../document_tool.class.php';
require_once dirname(__FILE__) . '/../document_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class DocumentToolPublisherComponent extends DocumentToolComponent
{

    function run()
    {
    	//every subscriber is allowed to add documents to dropbox??
        /*if (! $this->is_allowed(ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }*/
        
        $trail = new BreadcrumbTrail();
        if (Request :: get('pcattree') > 0)
        {
            foreach (Tool :: get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
            {
                $trail->add(new Breadcrumb($this->get_url(), $breadcrumb->get_name()));
            }
        }
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => DocumentTool :: ACTION_PUBLISH)), Translation :: get('Publish')));
        $trail->add_help('courses document tool');
        
        $pub = new ContentObjectRepoViewer($this, Document :: get_type_name());
        
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
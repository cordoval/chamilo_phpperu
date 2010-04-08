<?php
/**
 * $Id: link_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.link.component
 */
require_once dirname(__FILE__) . '/../link_tool.class.php';
require_once dirname(__FILE__) . '/../link_tool_component.class.php';
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class LinkToolPublisherComponent extends LinkToolComponent
{

    function run()
    {
        if (! $this->is_allowed(ADD_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
        
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses link tool');
        
        if (Request :: get('pcattree') != null)
        {
            foreach (Tool :: get_pcattree_parents(Request :: get('pcattree')) as $breadcrumb)
            {
            	if($breadcrumb)
                	$trail->add(new Breadcrumb($this->get_url(), $breadcrumb->get_name()));
            }
        }
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), Translation :: get('Publish')));
        $object = Request :: get('object');
        $pub = new ContentObjectRepoViewer($this, 'link', true);
        
        if (! isset($object))
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new ContentObjectPublisher($pub);
            $html[] = $publisher->get_publications_form($object);
        }
        
        $this->display_header($trail, true);
        echo implode("\n", $html);
        $this->display_footer();
    }
}
?>
<?php
/**
 * $Id: blog_publisher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.blog.component
 */
require_once dirname(__FILE__) . '/../../../content_object_repo_viewer.class.php';
require_once dirname(__FILE__) . '/../../../publisher/content_object_publisher.class.php';

class BlogToolPublisherComponent extends BlogToolComponent
{

    function run()
    {
        /*if (!$this->is_allowed(ADD_RIGHT))
		{
			Display :: not_allowed();
			return;
		}*/
        
/*        $trail = new BreadcrumbTrail();
        
        if (Request :: get('tool') == 'blog' && isset($_SESSION['blog_breadcrumbs']))
        {
            $breadcrumbs = $_SESSION['blog_breadcrumbs'];
            foreach ($breadcrumbs as $breadcrumb)
            {
                $trail->add(new Breadcrumb($breadcrumb['url'], $breadcrumb['title']));
            }
        }
        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_PUBLISH)), Translation :: get('Publisher')));
        $trail->add_help('courses blog tool');*/
        
        $component = ToolComponent :: factory(ToolComponent :: ACTION_PUBLISH, $this);
        $component->run();
    }
}
?>
<?php
/**
 * $Id: category_manager.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */
require_once dirname(__FILE__) . '/../forum_manager.class.php';
require_once dirname(__FILE__) . '/../../category_manager/forum_publication_category_manager.class.php';

class ForumManagerCategoryManagerComponent extends ForumManager implements DelegateComponent
{
    private $action_bar;

    function run()
    {
        if (! $this->is_allowed(VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }

        $category_manager = new ForumPublicationCategoryManager($this);
        $category_manager->run();
    
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('ForumManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('forum_category_manager');
    }
}
?>
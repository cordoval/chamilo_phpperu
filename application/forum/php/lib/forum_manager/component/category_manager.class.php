<?php
/**
 * $Id: category_manager.class.php 195 2009-11-13 12:02:41Z chellee $
 * @package application.lib.forum.forum_manager.component
 */
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
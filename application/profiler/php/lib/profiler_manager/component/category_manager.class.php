<?php namespace application\profiler
/**
 * $Id: category_manager.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */

/**
 * Profiler component allows the user to manage course categories
 */
class ProfilerManagerCategoryManagerComponent extends ProfilerManager implements DelegateComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $category_manager = new ProfilerCategoryManager($this);
        $category_manager->run();
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('profiler_category_manager');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('ProfilerManagerBrowserComponent')));
    }
    
}
?>
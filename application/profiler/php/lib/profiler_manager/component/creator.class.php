<?php

namespace application\profiler;

use common\extensions\repo_viewer\RepoViewerInterface;
use common\libraries\Request;
use common\libraries\Display;
use common\libraries\Translation;
use common\extensions\repo_viewer\RepoViewer;
use repository\content_object\profile\Profile;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Application;

/**
 * $Id: publisher.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component
 */
class ProfilerManagerCreatorComponent extends ProfilerManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        if (!Request :: get('category'))
        {
            $RIGHT_PUBLISH = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_PUBLISH, 0, 0);
        }
        else
        {
            $RIGHT_PUBLISH = ProfilerRights::is_allowed_in_profiler_subtree(ProfilerRights::RIGHT_PUBLISH, Request :: get('category'), ProfilerRights::TYPE_CATEGORY);
        }
        if(!$RIGHT_PUBLISH)
        {
            $this->display_header();
            Display :: warning_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $publisher = new ProfilePublisher($this);
            $publisher->get_publications_form(RepoViewer::get_selected_objects());
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Profile :: get_type_name());
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('profiler_creator');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => ProfilerManager :: ACTION_BROWSE_PROFILES)), Translation :: get('ProfilerManagerBrowserComponent')));
    }

 	function get_additional_parameters()
    {
    	return array('category');
    }
}

?>
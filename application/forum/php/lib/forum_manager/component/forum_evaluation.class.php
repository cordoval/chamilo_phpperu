<?php

namespace application\forum;

use common\libraries\Request;
use common\libraries\Translation;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;

//require_once WebApplication :: get_application_class_lib_path('gradebook') . 'evaluation_manager/evaluation_manager.class.php';

class ForumManagerForumEvaluationComponent extends ForumManager implements EvaluationManagerInterface
{
    private $publication_id;
    private $publisher_id;

    function run()
    {
        if (Request :: get(ForumManager :: PARAM_PUBLICATION_ID))
        {
            $forum_publication = $this->retrieve_forum_publication(Request :: get(ForumManager :: PARAM_PUBLICATION_ID));
            $this->publication_id = $forum_publication->get_id();
            $this->publisher_id = $forum_publication->get_author();
            
            EvaluationManager :: launch($this);
        }
        else
        {
            $this->display_error_message(Translation :: get('NoObjectsSelected'));
        }
    }

    function get_publication_id()
    {
        return $this->publication_id;
    }

    function get_publisher_id()
    {
        return $this->publisher_id;
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(ForumManager :: PARAM_ACTION => ForumManager :: ACTION_BROWSE)), Translation :: get('ForumManagerBrowserComponent')));
    	$breadcrumbtrail->add_help('forum_evaluation');
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_PUBLICATION_ID);
    }
}
?>
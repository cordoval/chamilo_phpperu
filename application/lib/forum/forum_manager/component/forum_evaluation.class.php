<?php
require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';
require_once dirname (__FILE__) . '/../forum_manager.class.php';

class ForumManagerForumEvaluationComponent extends ForumManager
{
    function run()
    {
        if (Request :: get(ForumManager :: PARAM_PUBLICATION_ID))
        {
        	$forum_publication = $this->retrieve_forum_publication(Request :: get(ForumManager :: PARAM_PUBLICATION_ID));
        	$publication_id = $forum_publication->get_id();
        	$publisher_id = $forum_publication->get_author();
    		$trail = BreadcrumbTrail :: get_instance();
        	$trail->add(new Breadcrumb($this->get_browse_forum_publications_url(), Translation :: get('BrowseForum')));
    		$trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE, ForumManager :: PARAM_PUBLICATION_ID => $publication_id)), Translation :: get('BrowseEvaluations')));
    		$this->set_parameter(ForumManager :: PARAM_PUBLICATION_ID, $publication_id);
			$evaluation_manager = new EvaluationManager($this, $publication_id, $publisher_id, Request :: get(EvaluationManager :: PARAM_EVALUATION_ACTION), $trail);
			$evaluation_manager->run();
        }  
        else
        {
            $this->display_error_message(Translation :: get('NoForumPublicationsSelected'));
        }
    }    
}
?>
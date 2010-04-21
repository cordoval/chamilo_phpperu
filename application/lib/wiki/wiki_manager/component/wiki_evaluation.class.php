<?php
require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';
require_once dirname (__FILE__) . '/../wiki_manager.class.php';

class WikiManagerWikiEvaluationComponent extends WikiManagerComponent
{
    function run()
    {
        if (Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION))
        {
        	$wiki_publication = $this->retrieve_wiki_publication(Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
        	$publication_id = $wiki_publication->get_id();
        	$publisher_id = $wiki_publication->get_publisher();
    		$trail = new BreadcrumbTrail();
        	$trail->add(new Breadcrumb($this->get_browse_wiki_publications_url(), Translation :: get('Wiki')));
    		$trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE, WikiManager :: PARAM_WIKI_PUBLICATION => $publication_id)), Translation :: get('BrowseEvaluations') . ' ' . $wiki_publication->get_content_object()->get_title()));
    		$this->set_parameter(WikiManager :: PARAM_WIKI_PUBLICATION, $publication_id);
			$evaluation_manager = new EvaluationManager($this, $publication_id, $publisher_id, Request :: get(EvaluationManager :: PARAM_EVALUATION_ACTION), $trail);
        }  
        else
        {
            $this->display_error_message(Translation :: get('NoWikiPublicationsSelected'));
        }
    }
}
?>
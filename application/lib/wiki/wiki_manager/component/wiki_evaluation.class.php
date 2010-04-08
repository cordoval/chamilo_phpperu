<?php
require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';

class WikiManagerWikiEvaluationComponent extends WikiManagerComponent
{
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('Wiki')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('WikiEvaluation')));
        $this->display_header($trail);
        if (Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION))
        {
        	$wiki_publication_id = Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION);
        	$wiki_publication = $this->retrieve_wiki_publication($wiki_publication_id);

			$evaluation_manager = new EvaluationManager($this, $wiki_publication, Request :: get('action'));
        }  
        else
        {
        	echo 'no wiki publication selected';
        }
        $this->display_footer();
    }    
}
?>
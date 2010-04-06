<?php
require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';
require_once dirname (__FILE__) . '/../../../gradebook/forms/evaluation_form.class.php';

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
        	$wiki_publication = $this->retrieve_wiki_publication(Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
        	
        	$evaluation_manager = new EvaluationManager($this);
        }  
        else
        {
        	echo 'no wiki publication selected';
        }
        $this->display_footer();
    }    
}
?>
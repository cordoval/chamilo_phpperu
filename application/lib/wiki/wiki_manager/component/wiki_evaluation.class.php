<?php
require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';

class WikiManagerWikiEvaluationComponent extends WikiManagerComponent
{
    function run()
    {
        if (Request :: get('parameters'))
        {
        	$parameter_string = Request :: get('parameters');
        	$parameters = unserialize(base64_decode($parameter_string));
        }
        if ($parameters[EvaluationManager :: PARAM_PUBLICATION_ID])
        {
        	$wiki_publication = $this->retrieve_wiki_publication($parameters[EvaluationManager :: PARAM_PUBLICATION_ID]);
        	$parameters[EvaluationManager :: PARAM_PUBLICATION_ID] = $wiki_publication->get_id();
        	$parameters[EvaluationManager :: PARAM_PUBLISHER_ID] = $wiki_publication->get_publisher();
    		$trail = new BreadcrumbTrail();
        	$trail->add(new Breadcrumb($this->get_browse_wiki_publications_url(), Translation :: get('Wiki')));
    		$trail->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE, EvaluationManager :: PARAM_PARAMETERS => $parameter_string)), Translation :: get('Evaluations') . ' ' . $wiki_publication->get_content_object()->get_title()));
			$evaluation_manager = new EvaluationManager($this, $parameters, Request :: get(EvaluationManager :: PARAM_EVALUATION_ACTION), $trail);
        }  
        else
        {
            $this->display_error_message(Translation :: get('NoWikiPublicationsSelected'));
        }
    }    
}
?>
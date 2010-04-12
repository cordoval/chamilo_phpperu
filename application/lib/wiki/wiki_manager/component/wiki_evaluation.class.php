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
			$evaluation_manager = new EvaluationManager($this, $wiki_publication, Request :: get('action'));
        }  
        else
        {
            $this->display_error_message(Translation :: get('NoWikiPublicationsSelected'));
        }
    }    
}
?>
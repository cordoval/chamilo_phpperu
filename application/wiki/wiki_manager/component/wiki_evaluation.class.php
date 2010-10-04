<?php
require_once dirname(__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';
require_once dirname(__FILE__) . '/../wiki_manager.class.php';

class WikiManagerWikiEvaluationComponent extends WikiManager implements EvaluationManagerInterface
{
    private $publication_id;
    private $publisher_id;

    function run()
    {
        if (Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION))
        {
            $wiki_publication = $this->retrieve_wiki_publication(Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
            $this->publication_id = $wiki_publication->get_id();
            $this->publisher_id = $wiki_publication->get_publisher();
            
            EvaluationManager :: launch($this);
        }
        else
        {
            $this->display_error_message(Translation :: get('NoWikiPublicationsSelected'));
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
    	$breadcrumbtrail->add_help('wiki_publication_evaluation');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('WikiManagerWikiPublicationsBrowserComponent')));
    }
    
    function get_additional_parameters()
    {
    	return array(self :: PARAM_WIKI_PUBLICATION);
    }
}
?>
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
            
            BreadcrumbTrail :: get_instance()->add(new Breadcrumb($this->get_url(array(EvaluationManager :: PARAM_EVALUATION_ACTION => EvaluationManager :: ACTION_BROWSE, WikiManager :: PARAM_WIKI_PUBLICATION => $publication_id)), Translation :: get('BrowseEvaluationsOf') . ' ' . $wiki_publication->get_content_object()->get_title()));
            $this->set_parameter(WikiManager :: PARAM_WIKI_PUBLICATION, $this->publication_id);
            
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
}
?>
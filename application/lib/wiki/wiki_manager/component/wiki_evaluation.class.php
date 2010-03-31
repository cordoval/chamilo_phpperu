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
   	    $wiki_publication = $this->retrieve_wiki_publication(Request :: get(WikiManager :: PARAM_WIKI_PUBLICATION));
   	    
   	    $pub_form = new EvaluationForm(EvaluationForm :: TYPE_CREATE, $wiki_publication, $this->get_url(array(WikiManager :: PARAM_WIKI_PUBLICATION => $wiki_publication->get_id(), 'validated' => 1)), $this->get_user());
		$pub_form->display();
		$this->display_footer();
    }

    /**
     * @see WikiManager :: display_header()
     */
	function display_header($breadcrumbtrail, $display_search = false)
    {
        return $this->get_parent()->display_header($breadcrumbtrail, $display_search);
    }
    
    /**
     * @see WikiManager :: display_footer()
     */
    function display_footer()
    {
        return $this->get_parent()->display_footer();
    }
        
	
}
?>
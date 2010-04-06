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
        if($pub_form->validate())
        {
        	$parameters['type'] = 'evaluation';
        	$parameters['publication_id'] = $wiki_publication->get_id();
        	$parameters['user_id'] = $wiki_publication->get_publisher();
        	$parameters['values'] = $pub_form->exportValues();
        	$evaluation_manager = new EvaluationManager($this, EvaluationManager :: ACTION_CREATE, $parameters);
        }
        $this->display_footer();
    }    
}
?>
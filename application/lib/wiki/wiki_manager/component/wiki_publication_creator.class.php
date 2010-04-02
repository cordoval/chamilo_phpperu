<?php
/**
 * $Id: wiki_publication_creator.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */
require_once dirname(__FILE__) . '/../wiki_manager.class.php';
require_once dirname(__FILE__) . '/../wiki_manager_component.class.php';
require_once dirname(__FILE__) . '/../../forms/wiki_publication_form.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/content_object_repo_viewer.class.php';

/**
 * Component to create a new wiki_publication object
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationCreatorComponent extends WikiManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('Wiki')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishWiki')));
        
        $objects = Request :: get('object');
        
        /*
         *  We make use of the ContentObjectRepoViewer setting the type to wiki
         */
        $pub = new RepoViewer($this, 'wiki', true);
        
        /*
         *  If no page was created you'll be redirected to the wiki_browser page, otherwise we'll get publications from the object
         */
        $this->display_header($trail, true);
        
        if (empty($objects))
        {
            echo $pub->as_html();
        }
        else
        {
            $form = new WikiPublicationForm(WikiPublicationForm :: TYPE_CREATE, null, $this->get_url(array('object' => $objects)), $this->get_user());
            if ($form->validate())
            {
                $values = $form->exportValues();
                
            	$failures = 0;
            	
            	if(!is_array($objects))
            		$objects = array($objects);
            	
                foreach($objects as $object)
                {
                	if(!$this->create_wiki_publication($object, $values))
                		$failures++;
                }
                $message = $this->get_result($failures, count($objects), 'WikiPublicationNotCreated', 'WikiPublicationsNotCreated', 'WikiPublicationCreated', 'WikiPublicationsCreated');               
                $this->redirect($message, $failures, array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS));
            }
            else
            {
                $form->display();
            }
        }
        
        //		echo implode("\n",$html);
        $this->display_footer();
    }
    
    function create_wiki_publication($object, $values)
    {
    	$wiki_publication = new WikiPublication();
		$wiki_publication->set_content_object($object);
		
        if ($values['forever'] != 0)
        {
            $wiki_publication->set_from_date(0);
            $wiki_publication->set_to_date(0);
        }
        else
        {
            $wiki_publication->set_from_date(Utilities :: time_from_datepicker($values['from_date']));
            $wiki_publication->set_to_date(Utilities :: time_from_datepicker($values['to_date']));
        }
        $wiki_publication->set_hidden($values['hidden'] ? 1 : 0);
        $wiki_publication->set_publisher($this->get_user_id());
        $wiki_publication->set_published(time());
        $wiki_publication->set_modified(time());
        $wiki_publication->set_display_order(0);
        $wiki_publication->create();
		if(Request :: post('evaluation'))
		{
			require_once dirname (__FILE__) . '/../../../gradebook/evaluation_manager/evaluation_manager.class.php';
			$parameters['type'] = 'internal_item';
			$parameters['application'] = Request :: get('application');
			$parameters['publication_id'] = $wiki_publication->get_id();
			$parameters['calculated'] = 'false';
			$evaluation_manager = new EvaluationManager($this, EvaluationManager :: ACTION_CREATE, $parameters);
		} 
        return $wiki_publication;
    }
}
?>
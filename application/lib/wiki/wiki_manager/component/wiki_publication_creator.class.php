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
        
        $object = Request :: get('object');
        
        /*
         *  We make use of the ContentObjectRepoViewer setting the type to wiki
         */
        $pub = new RepoViewer($this, 'wiki', true);
        
        /*
         *  If no page was created you'll be redirected to the wiki_browser page, otherwise we'll get publications from the object
         */
        $this->display_header($trail, true);
        
        if (empty($object))
        {
            echo $pub->as_html();
        }
        else
        {
            $wp = new WikiPublication();
            $wp->set_content_object($object);
            $form = new WikiPublicationForm(WikiPublicationForm :: TYPE_CREATE, $wp, $this->get_url(array('object' => $object, 'tool_action' => 'publish')), $this->get_user());
            if ($form->validate())
            {
                $success = $form->create_wiki_publication(); 
                $this->redirect($success ? Translation :: get('WikiPublicationCreated') : Translation :: get('WikiPublicationNotCreated'), (! $success ? true : false), array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS));
            }
            else
            {
                $form->display();
            }
        }
        
        //		echo implode("\n",$html);
        $this->display_footer();
    }
}
?>
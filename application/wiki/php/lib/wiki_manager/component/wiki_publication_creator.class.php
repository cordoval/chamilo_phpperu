<?php
/**
 * $Id: wiki_publication_creator.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('wiki') . 'wiki_manager/wiki_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('wiki') . 'forms/wiki_publication_form.class.php';
require_once WebApplication :: get_application_class_lib_path('weblcms') .'content_object_repo_viewer.class.php';

/**
 * Component to create a new wiki_publication object
 * @author Sven Vanpoucke & Stefan Billiet
 */
class WikiManagerWikiPublicationCreatorComponent extends WikiManager implements RepoViewerInterface
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        /*
         *  We make use of the ContentObjectRepoViewer setting the type to wiki
         */
        

        /*
         *  If no page was created you'll be redirected to the wiki_browser page, otherwise we'll get publications from the object
         */

        if (!RepoViewer::is_ready_to_be_published())
        {
            $repo_viewer = RepoViewer :: construct($this);
            $repo_viewer->run();
        }
        else
        {
            $objects = RepoViewer::get_selected_objects();
        	$form = new WikiPublicationForm(WikiPublicationForm :: TYPE_CREATE, null, $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $objects)), $this->get_user());
            if ($form->validate())
            {
                $values = $form->exportValues();
                $failures = 0;

                if (! is_array($objects))
                {
                    $objects = array($objects);
                }

                foreach ($objects as $object)
                {
                    if (! $form->create_wiki_publication($object, $values))
                        $failures ++;
                }
                $message = $this->get_result($failures, count($objects), 'WikiPublicationNotCreated', 'WikiPublicationsNotCreated', 'WikiPublicationCreated', 'WikiPublicationsCreated');
                $this->redirect($message, $failures, array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS));
            }
            else
            {
                $this->display_header(null, true);
                $form->display();
                $this->display_footer();
            }
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Wiki :: get_type_name());
    }
    
	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('wiki_publication_creator');
    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('WikiManagerWikiPublicationsBrowserComponent')));
    }
}
?>
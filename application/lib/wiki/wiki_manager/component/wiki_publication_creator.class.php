<?php
/**
 * $Id: wiki_publication_creator.class.php 210 2009-11-13 13:18:50Z kariboe $
 * @package application.lib.wiki.wiki_manager.component
 */
require_once dirname(__FILE__) . '/../wiki_manager.class.php';
require_once dirname(__FILE__) . '/../../forms/wiki_publication_form.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/content_object_repo_viewer.class.php';

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
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(WikiManager :: PARAM_ACTION => WikiManager :: ACTION_BROWSE_WIKI_PUBLICATIONS)), Translation :: get('Wiki')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('PublishWiki')));

        /*
         *  We make use of the ContentObjectRepoViewer setting the type to wiki
         */
        $repo_viewer = RepoViewer :: construct($this);

        /*
         *  If no page was created you'll be redirected to the wiki_browser page, otherwise we'll get publications from the object
         */

        if (! $repo_viewer->is_ready_to_be_published())
        {
            $repo_viewer->run();
        }
        else
        {
            $form = new WikiPublicationForm(WikiPublicationForm :: TYPE_CREATE, null, $this->get_url(array(RepoViewer :: PARAM_ACTION => RepoViewer :: ACTION_PUBLISHER, RepoViewer :: PARAM_ID => $repo_viewer->get_selected_objects())), $this->get_user());
            if ($form->validate())
            {
                $values = $form->exportValues();
                $failures = 0;

                $objects = $repo_viewer->get_selected_objects();

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
                $this->display_header($trail, true);
                $form->display();
                $this->display_footer();
            }
        }
    }

    function get_allowed_content_object_types()
    {
        return array(Wiki :: get_type_name());
    }
}
?>
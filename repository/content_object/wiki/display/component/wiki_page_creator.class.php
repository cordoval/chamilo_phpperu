<?php
/**
 * $Id: wiki_page_creator.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package repository.lib.complex_display.wiki.component
 */
/*
 * This is the compenent that allows the user to create a wiki_page.
 *
 * Author: Stefan Billiet
 * Author: Nick De Feyter
 */

require_once Path :: get_application_path() . 'lib/weblcms/content_object_repo_viewer.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/publisher/content_object_publisher.class.php';

class WikiDisplayWikiPageCreatorComponent extends WikiDisplay implements RepoViewerInterface
{
    private $publisher;

    function run()
    {
        

        if (!RepoViewer::is_ready_to_be_published())
        {
            $this->repo_viewer = RepoViewer :: construct($this);
            $this->repo_viewer->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, WikiDisplay :: ACTION_CREATE_PAGE);
            $this->repo_viewer->run();
        }
        else
        {
            $objects = RepoViewer::get_selected_objects();

            if (! is_array($objects))
            {
                $objects = array($objects);
            }

            foreach ($objects as $object)
            {
                $complex_content_object_item = ComplexContentObjectItem :: factory(WikiPage :: get_type_name());
                $complex_content_object_item->set_ref($object);
                $complex_content_object_item->set_parent($this->get_root_content_object()->get_id());
                $complex_content_object_item->set_user_id($this->get_user_id());
                $complex_content_object_item->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($this->get_root_content_object()->get_id()));
                $complex_content_object_item->set_is_homepage(0);
                $result = $complex_content_object_item->create();
            }

            $this->redirect(Translation :: get('WikiItemCreated'), '', array(
                    WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id()));
        }

    }

    function display_header()
    {
        parent :: display_header();

        $repo_viewer_action = Request :: get(RepoViewer :: PARAM_ACTION);

        switch ($repo_viewer_action)
        {
            case RepoViewer :: ACTION_BROWSER :
                $title = 'BrowseAvailableWikiPages';
                break;
            case RepoViewer :: ACTION_CREATOR :
                $title = 'CreateWikiPage';
                break;
            case RepoViewer :: ACTION_VIEWER :
                $title = 'PreviewWikiPage';
                break;
            default :
                $title = 'CreateWikiPage';
                break;
        }

        $html = array();
        $html[] = '<div class="wiki-pane-content-title">' . Translation :: get($title) . '</div>';
        $html[] = '<div class="wiki-pane-content-subtitle">' . Translation :: get('In') . ' ' . $this->get_root_content_object()->get_title() . '</div>';
        echo implode("\n", $html);
    }

    function get_allowed_content_object_types()
    {
        return array(WikiPage :: get_type_name());
    }
}
?>
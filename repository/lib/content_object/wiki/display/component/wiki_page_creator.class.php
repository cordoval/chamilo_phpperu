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
require_once Path :: get_repository_path() . 'lib/complex_builder/complex_repo_viewer.class.php';

class WikiDisplayWikiPageCreatorComponent extends WikiDisplay
{
    private $publisher;

    function run()
    {
        $this->publisher = new RepoViewer($this, WikiPage :: get_type_name(), RepoViewer :: SELECT_SINGLE);
        $this->publisher->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, WikiDisplay :: ACTION_CREATE_PAGE);

        if (!$this->publisher->is_ready_to_be_published())
        {
            $html[] = $this->publisher->as_html();
           
            $this->display_header($this->get_breadcrumbtrail());
            echo implode("\n", $html);
            $this->display_footer();

        }
        else
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($this->publisher->get_selected_objects());
            $count = RepositoryDataManager :: get_instance()->count_type_content_objects(WikiPage :: get_type_name(), new EqualityCondition(ContentObject :: PROPERTY_TITLE, $content_object->get_title()));
            if ($count == 1)
            {
                $complex_content_object_item = ComplexContentObjectItem :: factory(WikiPage :: get_type_name());
                $complex_content_object_item->set_ref($this->publisher->get_selected_objects());
                $complex_content_object_item->set_parent($this->get_root_content_object()->get_id());
                $complex_content_object_item->set_user_id($this->publisher->get_user_id());
                $complex_content_object_item->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($this->get_root_content_object()->get_id()));
                $complex_content_object_item->set_additional_properties(array('is_homepage' => 0));
                $complex_content_object_item->create();
                $this->redirect(Translation :: get('WikiItemCreated'), '', array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_VIEW_WIKI_PAGE, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $complex_content_object_item->get_id(), 'pid' => $this->get_root_content_object()->get_id()));
            }
            else
            {
                $this->display_header($this->get_breadcrumbtrail());
                $this->display_error_message(Translation :: get('WikiPageTitleError'));
                echo $this->publisher->as_html();
                $this->display_footer();
            }
        }

    }
}
?>
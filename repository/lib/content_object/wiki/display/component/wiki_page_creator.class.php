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

class WikiDisplayWikiPageCreatorComponent extends WikiDisplay
{
    private $publisher;

    function run()
    {
        $this->repo_viewer = new RepoViewer($this, WikiPage :: get_type_name());
        $this->repo_viewer->set_parameter(ComplexDisplay :: PARAM_DISPLAY_ACTION, WikiDisplay :: ACTION_CREATE_PAGE);
        
        if (! $this->repo_viewer->is_ready_to_be_published())
        {
            $this->repo_viewer->run();
        }
        else
        {
            $objects = $this->repo_viewer->get_selected_objects();
            foreach($objects as $object)
            {
            	$complex_content_object_item = ComplexContentObjectItem :: factory(WikiPage :: get_type_name());
                $complex_content_object_item->set_ref($object);
                $complex_content_object_item->set_parent($this->get_root_content_object()->get_id());
                $complex_content_object_item->set_user_id($this->get_user_id());
                $complex_content_object_item->set_display_order(RepositoryDataManager :: get_instance()->select_next_display_order($this->get_root_content_object()->get_id()));
                $complex_content_object_item->set_is_homepage(0);
                $complex_content_object_item->create();
            }
            
            $this->redirect(Translation :: get('WikiItemCreated'), '', array(WikiDisplay :: PARAM_DISPLAY_ACTION => WikiDisplay :: ACTION_BROWSE_WIKIS));
        }
    
    }
}
?>
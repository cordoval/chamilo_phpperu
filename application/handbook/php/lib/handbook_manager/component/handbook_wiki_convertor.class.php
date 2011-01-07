<?php


namespace application\handbook;

use common\extensions\repo_viewer\ContentObjectTable;
use repository\content_object\wiki\Wiki;
use common\libraries\Application;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\ObjectTableFormActions;
use common\libraries\ObjectTableFormAction;
use common\libraries\Utilities;
use common\libraries\Request;
use repository\RepositoryDataManager;
use repository\content_object\handbook\Handbook;
use common\libraries\EqualityCondition;
use repository\ComplexContentObjectItem;
use repository\content_object\handbook_item\HandbookItem;
use repository\content_object\handbook_topic\HandbookTopic;



require_once dirname(__FILE__) . '/handbook_wiki_convertor/wiki_convertor_table.class.php';
/**
 * Component to import content from a wiki with wikipages to a handbook with handbooktopics
 */
class HandbookManagerHandbookWikiConvertorComponent extends HandbookManager
{

    const PARAM_SELECTED_WIKI = 'wiki_id';


    const ACTION_ADD_WIKIS = "add_wiki";
    
    private $last_created;


    /**
     * Runs this component and displays its output.
     */
    function run()
    {

            $table = $this->get_table();
            


            $table_name = 'content_object_table';
            if(WikiConvertorContentObjectTable::get_selected_ids($table_name) == null)
            {
                //no selection: show table
                $html[] = $table;
            }
            else
            {
               $success = $this->convert_wikis(WikiConvertorContentObjectTable::get_selected_ids($table_name));


                $redirect_params = array();
                $redirect_params[HandbookManager :: PARAM_ACTION] = HandbookManager::ACTION_VIEW_HANDBOOK;
                $redirect_params[HandbookManager :: PARAM_HANDBOOK_PUBLICATION] = Request::get(HandbookManager :: PARAM_HANDBOOK_PUBLICATION);
                $redirect_params[HandbookManager :: PARAM_HANDBOOK_ID] = Request::get(HandbookManager :: PARAM_HANDBOOK_ID);
                $redirect_params[HandbookManager :: PARAM_TOP_HANDBOOK_ID] = Request::get(HandbookManager :: PARAM_TOP_HANDBOOK_ID);
                
                //TODO: id of created handbook
                $redirect_params[HandbookManager :: PARAM_HANDBOOK_SELECTION_ID] = Request::get(HandbookManager :: PARAM_HANDBOOK_SELECTION_ID);
                $this->redirect($success ? Translation :: get('WikiConverted') : Translation :: get('WikiNotConverted'), ! $success, $redirect_params);

            }

            $this->display_header();
            echo implode("\n", $html);
            $this->display_footer();
    }

    private function convert_wikis($ids_array)
    {

        $success = true;
        
        foreach ($ids_array as $wiki_id)
        {
            $rdm = RepositoryDataManager::get_instance();
            $selected_wiki = $rdm->retrieve_content_object($wiki_id);

            //create new handbook from wiki-data
            $handbook = new Handbook();
            $handbook->set_title($selected_wiki->get_title());
            $handbook->set_description($selected_wiki->get_description());
            $handbook->set_owner_id($this->get_user_id());
            $success &= $handbook->create();
             

            $parent_handbook_id = $handbook->get_id();


            //create handbook-topics from wiki-pages
            $selected_wiki_page_ids = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $wiki_id, ComplexContentObjectItem :: get_table_name()));

           
            while($complex_wiki_page = $selected_wiki_page_ids->next_result())
            {
                $wiki_page = $complex_wiki_page->get_ref_object();
                $handbook_topic = new HandbookTopic();
                $handbook_topic->set_title($wiki_page->get_title());
                $handbook_topic->set_text($wiki_page->get_description());
                $handbook_topic->set_owner_id($this->get_user_id());
                $success &= $handbook_topic->create();
                 

                //add handbook-topic to new handbook
                $handbook_item = new HandbookItem();
                $handbook_item->set_title(HandbookItem :: get_type_name());
                $handbook_item->set_description(HandbookItem :: get_type_name());
                $handbook_item->set_owner_id($this->get_user_id());
                $handbook_item->set_reference($handbook_topic->get_id());
                $handbook_item->set_parent_id(0);
                $success &= $handbook_item->create();
                 

                $complex_content_object_item = ComplexContentObjectItem :: factory(HandbookItem :: get_type_name());
                $complex_content_object_item->set_ref($handbook_item->get_id());
                $complex_content_object_item->set_parent($parent_handbook_id);
                $complex_content_object_item->set_display_order($rdm->select_next_display_order($parent_handbook_id));
                $complex_content_object_item->set_user_id($this->get_user_id());
                $success &= $complex_content_object_item->create();
                 

//                $this->last_created = $handbook_topic->get_id();
            }       
            //add new handbook to published handbook
            $complex_content_object_item_handbook = ComplexContentObjectItem :: factory(HandbookItem :: get_type_name());
            $complex_content_object_item_handbook->set_ref($handbook->get_id());
            $complex_content_object_item_handbook->set_parent(Request::get(HandbookManager :: PARAM_HANDBOOK_ID));
            $complex_content_object_item_handbook->set_display_order($rdm->select_next_display_order(Request::get(HandbookManager :: PARAM_HANDBOOK_ID)));
            $complex_content_object_item_handbook->set_user_id($this->get_user_id());
            $success &= $complex_content_object_item_handbook->create();

            

        }
        
        return $success;
    }

    private function get_table()
    {
//        $parameters = $this->get_parameters(true);
//        $parameters[ActionBarSearchForm :: PARAM_SIMPLE_SEARCH_QUERY] = $this->action_bar->get_query();
        $parameters[Application :: PARAM_APPLICATION] = 'handbook';
//        $parameters[Application :: PARAM_ACTION] = HandbookManager :: ACTION_BROWSE;

        $actions = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
//        $action_items[] = new ToolbarItem(Translation :: get('ViewHandbookPreferences'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => self::APPLICATION_NAME, self :: PARAM_ACTION => self :: ACTION_VIEW_PREFERENCES, self :: PARAM_HANDBOOK_PUBLICATION_ID => $this->handbook_publication_id)));
//        $actions->add_items($action_items);

        $types = array(Wiki::get_type_name());
        $query = null;

        $table = new WikiConvertorContentObjectTable($this, $this->get_user(), $types, $query, $actions);

         
         
        $form_actions = new ObjectTableFormActions($namespace, $action, $form_actions);
        $form_actions->add_form_action(new ObjectTableFormAction(self::ACTION_ADD_WIKIS, Translation :: get('ConvertWikis', null, Utilities :: COMMON_LIBRARIES)));

        $table->set_form_actions($form_actions);
        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");

    }

    function get_maximum_select()
    {
        return 0;
    }

    function is_shared_object_browser()
    {
        return 0;
    }

    function get_excluded_objects()
    {
        return array();
    }

    function get_query()
    {
        return null;
    }
   

}
?>
<?php
namespace application\handbook;
use common\libraries\Request;
use repository\RepositoryDataManager;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Theme;
use common\libraries\Application;
use application\context_linker\ContextLinkerManager;
use repository\content_object\handbook_item\HandbookItem;
use repository\content_object\handbook\Handbook;
use application\metadata\MetadataManager;
use repository\ContentObjectDisplay;
use repository\content_object\document\Document;
use repository\RepositoryManager;
use common\libraries\Utilities;



require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../handbook_menu.class.php';

//require_once dirname(__FILE__).'/../../../context_linker/php/context_link.class.php';
//require_once dirname(__FILE__).'/../../../context_linker/php/context_linker_data_manager.class.php';
//require_once dirname(__FILE__).'/../../../context_linker/php/context_linker_manager/context_linker_manager.class.php';
//require_once dirname(__FILE__).'/../../../metadata/php/metadata_manager/metadata_manager.class.php';
/**
 * Component to view a handbook and it's content
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookViewerComponent extends HandbookManager
{

    const PARAMETER_SHOW_ALTERNATIVES = 'sa';
    const ALL_ALTERNATIVES = 'aa';
    const RELEVANT_ALTERNATIVES_ONLY = 'ro';





    private $handbook_id;
    private $handbook_selection_id;
    private $selected_object;



    private $user_preferences = array();
    private $handbook_preferences = array();



	/**
     * Runs this component and displays its output.
     */
    function run()
    {
        //GET CONTENT OBJECTS TO DISPLAY
        $this->get_content_objects();
        $this->get_preferences();

        parent::display_header();

        //ACTIONBAR
        $this->action_bar = $this->get_action_bar();
        $html[] = $this->action_bar->as_html();

        //MENU
        $html[] = $this->get_menu();

        //CONTENT
        $html[] = '<div>';
        $html[] = $this->display_content();
        $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '</div>';

        echo implode ("\n", $html);
        parent::display_footer();
    }

    function get_allowed_content_object_types()
    {
        return array(Handbook :: get_type_name());
    }

    function get_content_objects()
    {
        $this->handbook_id = Request :: get(HandbookManager::PARAM_HANDBOOK_ID);
        $this->handbook_selection_id = Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);

        $rdm = RepositoryDataManager::get_instance();

        if ($this->handbook_id && $this->handbook_selection_id)
        {
            $this->selected_object = $rdm->retrieve_content_object($this->handbook_selection_id);
            if ($this->selected_object && $this->selected_object->get_type() == HandbookItem::get_type_name())
            {
                $this->selected_object = $rdm->retrieve_content_object($this->selected_object->get_reference());
            }
        }
        elseif ($this->handbook_id && ! $this->handbook_selection_id)
        {
            $publication = HandbookDataManager :: get_instance()->retrieve_handbook_publication($this->handbook_id);
            if($publication)
            {
                $this->selected_object = $rdm->retrieve_content_object($publication->get_content_object_id());
            }
        }
    }

    function get_menu()
    {
        $html[] ='<div id="tool_browser_left">';

            $html[] = '<div id="tool_bar" class="tool_bar tool_bar_left">';

                $html[] = '<div id="tool_bar_hide_container" class="hide">';
                    $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_left_hide.png" /></a>';
                    $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_left_show.png" /></a>';
                $html[] = '</div>';

                $html[] = '<div>';
                    $menu = new HandbookMenu( 'run.php?application='.self::ACTION_VIEW_HANDBOOK.'&application=handbook&'. HandbookManager::PARAM_HANDBOOK_ID.'='.$this->handbook_id, $this->handbook_id);
                    $html[] = $menu->render_as_tree();
                $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'libraries/resources/javascript/tool_bar.js' . '"></script>';
            $html[] = '<div class="clear"></div>';

            return implode ("\n", $html);
    }

    function display_content()
    {
//        //VOORLOPIG: print preferences
//         $html[] = '<div>';
//         $html[] = 'user preferences:<br/>';
//         while(list($key, $value)= each($this->user_preferences))
//         {
//             $html[] = $key . ' =  '. $value . '<br/>';
//         }
//         $html[] = 'handbook preferences:<br/>';
//         while(list($key, $value)= each($this->handbook_preferences))
//         {
//             $html[] = $key . ' =  '. $value . '<br/>';
//         }
//         $html[] = '</div>';

        if ($this->selected_object && $this->selected_object->get_type() == Handbook::get_type_name())
        {
            //SHOW ALL ITEMS IN THIS HANDBOOK (one level)
            $html[] = $this->get_handbook_html($this->selected_object->get_id());
        }
        else if($this->selected_object)
        {
           //SHOW ONLY THE SELECTED ITEM
             $html[] = $this->get_item_html($this->selected_object->get_id());
        }

        return implode ("\n", $html);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        //edit handbook/item

        //delete handbook/item

        //add handbook/item

        //set handbook rights

        if($this->selected_object)
        {
            //create alternative context version
            $actions[] = new ToolbarItem(Translation :: get('CreateContextLink'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => ContextLinkerManager::APPLICATION_NAME, ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => $this->selected_object->get_id())));
        }
        //view glossary



        $action_bar->set_common_actions($actions);

        return $action_bar;

    }



    /**
     * retrieve all the glossary's in this handbook publication and combine them in one
     * searchable table
     */
    function get_glossary()
    {

    }


    function print_metadata($co_id)
    {
        $metadata = MetadataManager::retrieve_metadata_for_content_object($co_id);
        while(list($key, $value)= each($metadata))
         {
             $html[] = $key . ' =  '. $value . '<br/>';
         }
         return implode ("\n", $html);
    }

    function get_item_html($co_id, $show_alternatives_button = true)
    {

        $alternatives_array = HandbookManager::get_alternatives($co_id, $this->handbook_id);

         $text_width;
         $visual_width;

         if($alternatives_array['image_main'] != null|| $alternatives_array['video_main'] != null)
         {
             $text_width = '67%';
             $visual_width = '33%';
         }
         else
         {
             $text_width = '100%';
             $visual_width = '0%';
         }

         //OUTPUT HTML
        $html[] = '<div class = "handbook_item" style="float:left; background:green; padding:10px;">';

            $html[] = '<div class = "handbook_item_primary_info" style="float:left; background:pink; width:'.$text_width.';">';
                if($alternatives_array['text_main'] != null)
                {
                    $html[] = '<div class = "handbook_item_text" style="float:left; width:'.'100%'.';">';
                    //TEXT
                    //MAIN
                    $display = ContentObjectDisplay :: factory($alternatives_array['text_main']);
//                    $html[] = $display->get_full_html();
                    $html[] = '<div class="main_title">';
                    $html[] = $alternatives_array['text_main']->get_title();
                     $html[] = '</div>';
                    $html[] = '<div class="main_text">';
                    $html[] = $alternatives_array['text_main']->get_text();
                     $html[] = '</div>';
                    $html[] = '</div>';
                    //ALTERNATIVES
                    if(count($alternatives_array['text'])>0 )
                    {
                        $html[] = '<br /><a href="#" id="showtext" style="display:block; float:left;">' . Translation :: get('ShowAllTextAlternatives') . '</a><br><br>';
                        $html[] = '<a href="#" id="hidetext" style="display:none; font-size: 80%; font-weight: normal;">(' . Translation :: get('HideAllTextAlternatives') . ')</a>';
                        $html[] = '<div id="textlist" style="display:none;">';

                        while(list($key, $value)= each($alternatives_array['text']))
                         {
                            $html[] = '<div class="alternative_metadata">';
                             $html[] = $this->print_metadata($value->get_id());
                             $html[] = '</div>';
                             $display = ContentObjectDisplay :: factory($value);
//                             $html[] = $display->get_full_html();
                             $html[] = '<div class="alternative_title">';
                             $html[] = $value->get_title();
                             $html[] = '</div>';
                             $html[] = '<div class="alternative_text">';
                            $html[] = $value->get_text();
                            $html[] = '</div>';
                             $html[] = '</div>';
                         }
                         $html[] = '</div>';
                    }
//                    $html[] = '</div>';
                }


               //IMAGES START
                $html[] = '<div class = "handbook_item_visual" style="float:left; background:blue; width:'.$visual_width.'">';
                if($alternatives_array['image_main'] != null)
                {
                    $html[] = '<div class = "handbook_item_images" style="padding: 10px;">';
                    //IMAGES
                    //MAIN
                    $object = $alternatives_array['image_main'];
                    $url = Path :: get(WEB_PATH) . RepositoryManager :: get_document_downloader_url($object->get_id());
                    //TODO SHOW POPUP WITH LARGER PIC ON CLICK INSTEAD OF DOWNLOAD
                   $html[] = '<div>';
                    $html[] = '<a href="'.$url.'"><img style = "max-width:100%" src="'.$url.'"></a>';
                    $html[] = '</div>';
                   
                    


                    
                    //ALTERNATIVES
                    if(count($alternatives_array['image'])>0)
                    {
                        $html[] = '<br /><a href="#" id="showimage" style="display:none; float:left;">' . Translation :: get('ShowAllImageAlternatives') . '</a><br><br>';
                        $html[] = '<a href="#" id="hideimage" style="display:none; font-size: 80%; font-weight: normal;">(' . Translation :: get('HideAllImageAlternatives') . ')</a>';
                        $html[] = '<div id="imagelist">';

                     while(list($key, $value)= each($alternatives_array['image']))
                         {
                             $html[] = $this->print_metadata($value->get_id());
                             $display = ContentObjectDisplay :: factory($value);
                             $html[] = $display->get_description();
                         }
                         $html[] = '</div>';
                    $html[] = '</div>';
                    }
                }
                if($alternatives_array['video_main'] != null)
                {
                    $html[] = '<div class = "handbook_item_videos" style="padding: 10px;">';
                    //VIDEO
                    //MAIN
                    $display = ContentObjectDisplay :: factory($alternatives_array['video_main']);
                    $html[] = $display->get_preview(true);
                    $html[] = '</div>';
                    //ALTERNATIVES
                    if(count($alternatives_array['video'])>0)
                    {
                        $html[] = '<br /><a href="#" id="showvideo" style="display:none; float:left;">' . Translation :: get('ShowAllVideoAlternatives') . '</a><br><br>';
                        $html[] = '<a href="#" id="hidevideo" style="display:none; font-size: 80%; font-weight: normal;">(' . Translation :: get('HideAllVideoAlternatives') . ')</a>';
                        $html[] = '<div id="videolist">';

                        while(list($key, $value)= each($alternatives_array['video']))
                         {

                             $html[] = $this->print_metadata($value->get_id());
                             $display = ContentObjectDisplay :: factory($value);
                             $html[] = $display->get_preview(true);
                             //todo: link to view video in full size (popup?)
                         }
                        $html[] = '</div>';
                    }
                }
                $html[] = '</div>';
//                $html[] = '</div>';
//            $html[] = '</div>';
//            $html[] = '</div>';

            $html[] = '<div class = "handbook_item_secondary_info" style="float:left; background:red;">';
            $html[] = 'secondary';
            if(count($alternatives_array['link'])>0)
            {
              //SHOW LINKS
                 $html[] = '<div class = "handbook_links" style="width:50%; float:left;">';
                 $html[] = '<H1>LINKS</H1>';
                 while(list($key, $value)= each($alternatives_array['link']))
                 {
                     $html[] = $this->print_metadata($value->get_id());
                     $display = ContentObjectDisplay :: factory($value);
                     $html[] = $display->get_description();
//                     $html[] = '</div>';

                 }
                 $html[] = '</div>';
            }
            if(count($alternatives_array['other'])>0)
            {
                 //SHOW OTHERS
                 $html[] = '<div class = "handbook_others" style="width:50%; clear:both;">';
                 $html[] = '<H1>OTHER</H1>';
                 while(list($key, $value)= each($alternatives_array['other']))
                 {
                     $html[] = $this->print_metadata($value->get_id());

                     $display = ContentObjectDisplay :: factory($value);
                     
                     $html[] = $display->get_full_html();
                     $html[] = '</div>';
                 }
                 $html[] = '</div>';
            }
            $html[] = '</div>';

        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_PATH) .'application/'. $this->get_application_name() . '/resources/javascript/handbook_alternatives.js' . '"></script>';
//        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_PATH) . 'common/resources/javascript/handbook_alternatives.js' . '"></script>';

        return implode ("\n", $html);

    }

    function get_handbook_html($co_id, $show_alternatives_button = true)
    {
        //GET ITEM ALTERNATIVES
//         $cldm = ContextLinkerDataManager::get_instance();
//         $rdm = RepositoryDataManager::get_instance();
//         $condition = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $co_id);
//         $context_links_resultset = $cldm->retrieve_full_context_links($condition);

        //DETERMINE MOST SUITABLE ALTERNATIVE

        $alternatives_array = HandbookManager::get_alternatives($co_id, $this->handbook_id);

         //DISPLAY TITLES
         //todo: hide alternatives
//         $display = ContentObjectDisplay :: factory($alternatives_array['handbook_main']);
//        $html[] = $display->get_full_html();
//        $html[] = '</div>';

         $html[] = '<H1>'.$alternatives_array['handbook_main']->get_title().'</H1>';
         $html[] = $alternatives_array['handbook_main']->get_description();
         $html[] = 'alternative titles: ';
         while(list($key, $value)= each($alternatives_array['handbook']))
        {
//             $html[] = $this->print_metadata($value->get_id());
//             $display = ContentObjectDisplay :: factory($value);
//             $html[] = $display->get_full_html();
//             $html[] = '</div>';
             $html[] = '  -  ';
             $html[] = $value->get_title();


         }



         //DISPLAY ITEMS
         //todo: display the items in this handbook?

         return implode ("\n", $html);

    }




////    function get_preferences($handbook_publication_id)
//    {
//        //USER PREFERENCES
//        //TODO: This should be gotten from a user-metadata table for now only the language and the publi is taken into account
//        $this->user_preferences[self::PARAM_LANGUAGE] = $this->translate_chamilo_language_to_iso_code(Translation::get_instance()->get_language());
//
//        //for now: get institution name from root group
//        $condition = new EqualityCondition(Group :: PROPERTY_PARENT, 0);
//        $group = GroupDataManager :: get_instance()->retrieve_groups($condition, null, 1, new ObjectTableOrder(Group :: PROPERTY_NAME))->next_result();
//        $this->user_preferences[self::PARAM_PUBLISHER] = $group->get_name();
//
//        //HANDBOOK PREFERENCES
//        //TODO: this should be gotten from a handbook-publication-preferences table
//
//        return;
//    }

}
?>
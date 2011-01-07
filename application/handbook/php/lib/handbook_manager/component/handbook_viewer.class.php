<?php


namespace application\handbook;
use common\libraries\Request;
use repository\RepositoryDataManager;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Utilities;
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
use rights\RightsUtilities;
use common\libraries\EqualityCondition;
use repository\ComplexContentObjectItem;
use repository\content_object\glossary\Glossary;
use repository\content_object\handbook_topic\HandbookTopic;
use repository\content_object\wiki\Wiki;
use common\libraries\DynamicContentTab;
use common\libraries\DynamicTabsRenderer;



require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../handbook_menu.class.php';
require_once dirname(__FILE__).'/../../handbook_rights.class.php';

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
    const SESSION_PARAMETER_PUBLICATION_ID = 'HPI';

    const METADATA_SHORT = 0;
    const METADATA_LONG = 1;

    private $handbook_publication_id; //the id of the publication
    private $handbook_id; //the id of the parent handbook for the selection
    private $handbook_selection_id; //the id of the current selection
    private $complex_selection_id; //the id of the complex content object item wrapper
    private $top_handbook_id; //the id of the top handbook
    private $selected_object;
    private $user_preferences = array();
    private $handbook_preferences = array();

    private $next_item_id;
    private $previous_item_id;

    private $edit_right;
    private $view_right;



	/**
     * Runs this component and displays its output.
     */
    function run()
    {
        //GET CONTENT OBJECTS TO DISPLAY
        $this->get_rights();
        if($this->view_right)
        {
            $this->get_content_objects();
            $this->get_preferences($this->handbook_id);

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
        else
        {
            parent::display_header();
            $html[] = '<div>';
            $html[] = $this->display_not_allowed();
            $html[] = '</div>';
            echo implode ("\n", $html);
            parent::display_footer();

        }

    }

    function get_allowed_content_object_types()
    {
        return array(Handbook :: get_type_name());
    }

    function get_rights()
    {
        $user_id = $this->get_user_id();
        $this->handbook_publication_id = Request :: get(HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID);
        if(!$this->handbook_publication_id)
        {
            $this->check_for_uid();
        }
//        $location_id = HandbookRights::get_location_id_by_identifier_from_handbooks_subtree($this->handbook_publication_id);
        $this->view_right = HandbookRights::is_allowed_in_handbooks_subtree(HandbookRights::VIEW_RIGHT, $this->handbook_publication_id, $user_id);
        $this->edit_right = HandbookRights::is_allowed_in_handbooks_subtree(HandbookRights::EDIT_RIGHT, $this->handbook_publication_id, $user_id);
        
    }

    function check_for_uid()
    {
        $this->uid = Request :: get('uid');
        $this->handbook_publication_id = $_SESSION[self::SESSION_PARAMETER_PUBLICATION_ID];

        $hdm = HandbookDataManager::get_instance();

        
        $item_data = $hdm->retrieve_handbook_item_data_by_uuid($this->uid);
        $this->handbook_selection_id = $item_data[HandbookItem::PROPERTY_ID];
       

        if(!$this->handbook_publication_id)
        {
            //publication unknown ->TODO search for possible publications (that contain this item) and let user select the one to display
            var_dump('no publication');
        }
        else
        {
            $hdm = HandbookDataManager::get_instance();
            $pub = $hdm->retrieve_handbook_publication($this->handbook_publication_id);
            $this->top_handbook_id = $pub->get_content_object_id();
        }
                


    }

    function get_content_objects()
    {
        $this->handbook_id = Request :: get(HandbookManager::PARAM_HANDBOOK_ID);
        if(!$this->handbook_selection_id)
        {
            $this->handbook_selection_id = Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);
        }
        $this->complex_selection_id =  Request :: get(HandbookManager::PARAM_COMPLEX_OBJECT_ID);
        if(!$this->top_handbook_id)
        {
            $this->top_handbook_id =  Request :: get(HandbookManager::PARAM_TOP_HANDBOOK_ID);
        }
        if(!$this->handbook_id)
        {
            $this->handbook_id =  $this->top_handbook_id;
        }


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
        

        $_SESSION[self::SESSION_PARAMETER_PUBLICATION_ID] = $this->handbook_publication_id;
        $this->get_next_previous_items();
       
    }

    function get_next_previous_items()
    {
        //TODO FINISH THIS FUNCTION
        if($this->selected_object != null  && $this->complex_selection_id != null )
        {
            $rdm = RepositoryDataManager::get_instance();

    //        $conditions[] = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $this->handbook_id);
//            $condition = new EqualityCondition(ComplexContentObjectItem :: PROPERTY_ID, $this->selected_object->get_id());

            $complex_content_item = $rdm->retrieve_complex_content_object_item($this->complex_selection_id);
            $display_order = $complex_content_item->get_display_order();

            $next = $display_order +1;
            $previous = $display_order -1;

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
                $hpid = $this->handbook_publication_id;

//                $menu = new HandbookMenu( 'run.php?application='.self::ACTION_VIEW_HANDBOOK.'&application=handbook&'. HandbookManager::PARAM_HANDBOOK_ID.'='.$this->handbook_id,  $this->top_handbook_id, null, $this->handbook_publication_id, $this->handbook_id);
               $menu = new HandbookMenu( '',  $this->handbook_id, $this->handbook_selection_id, $this->handbook_publication_id, $this->top_handbook_id);
                    $html[] = $menu->render_as_tree();
                $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'libraries/resources/javascript/tool_bar.js' . '"></script>';
            $html[] = '<div class="clear"></div>';

            return implode ("\n", $html);
    }

    function display_not_allowed()
    {
        $html[] = 'You are not allowed to view this handbook';
        return implode ("\n", $html);
    }

    function display_preferences()
    {
//        $html[] = '<div>';
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
//         return implode ("\n", $html);
    }

    function display_content()
    {
//        //VOORLOPIG: print preferences
//        $html[] = $this->display_preferences();
         

        if ($this->selected_object && $this->selected_object->get_type() == Handbook::get_type_name())
        {
            //SHOW ALL ITEMS IN THIS HANDBOOK (one level)
            //TODO: implement
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
        if($this->edit_right)
        {
            $actions[] = new ToolbarItem(Translation :: get('EditPublicationRights'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => self::APPLICATION_NAME, self :: PARAM_ACTION => self :: ACTION_EDIT_RIGHTS, self :: PARAM_HANDBOOK_PUBLICATION_ID => $this->handbook_publication_id)));
            $actions[] = new ToolbarItem(Translation :: get('ViewHandbookPreferences'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => self::APPLICATION_NAME, self :: PARAM_ACTION => self :: ACTION_VIEW_PREFERENCES, self :: PARAM_HANDBOOK_PUBLICATION_ID => $this->handbook_publication_id)));

            $actions[] = new ToolbarItem(Translation :: get('AddNewItemToHandbook'), Theme :: get_content_object_image_path(HandbookTopic::get_type_name()), $this->get_create_handbook_item_url($this->handbook_id, $this->top_handbook_id, $this->handbook_publication_id), ToolbarItem :: DISPLAY_ICON_AND_LABEL);

            $actions[] = new ToolbarItem(Translation :: get('ConvertWiki'), Theme :: get_content_object_image_path(Wiki::get_type_name()), $this->get_convert_wiki_to_handbook_item_url($this->handbook_id, $this->top_handbook_id, $this->handbook_publication_id, $this->handbook_selection_id), ToolbarItem :: DISPLAY_ICON_AND_LABEL);

        }

        if($this->selected_object && $this->edit_right)
        {
            //create alternative context version
//            $redirect_url = 'handbook_viewer&application=handbook&thid='.$this->top_handbook_id.'&hid='.$this->handbook_id.'&hpid='.$this->handbook_publication_id.'&hsid='.$this->handbook_selection_id;
            $redirect_url = array();
            $redirect_url[Application :: PARAM_APPLICATION] = 'handbook';
            $redirect_url[Application :: PARAM_ACTION] = HandbookManager :: ACTION_VIEW_HANDBOOK;
            $redirect_url[HandbookManager::PARAM_TOP_HANDBOOK_ID] = $this->top_handbook_id;
            $redirect_url[HandbookManager::PARAM_HANDBOOK_ID] = $this->handbook_id;
            $redirect_url[HandbookManager::PARAM_HANDBOOK_SELECTION_ID] = $this->handbook_selection_id;

            $actions[] = new ToolbarItem(Translation :: get('CreateContextLink'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => ContextLinkerManager::APPLICATION_NAME, ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => $this->selected_object->get_id(), ContextLinkerManager::PARAM_REDIRECT_URL => $redirect_url)));


            $params = array();
            $params[Application::PARAM_APPLICATION] = HandbookManager::APPLICATION_NAME;
            $params[HandbookManager :: PARAM_ACTION] = HandbookManager :: ACTION_PICK_ITEM_TO_EDIT;
            $params[HandbookManager :: PARAM_HANDBOOK_PUBLICATION_ID] = $this->handbook_publication_id;
            $params[HandbookManager :: PARAM_HANDBOOK_ID] = $this->handbook_id; 
            $params[HandbookManager :: PARAM_HANDBOOK_SELECTION_ID] = $this->handbook_selection_id;
            $params[HandbookManager :: PARAM_COMPLEX_OBJECT_ID] = $this->complex_selection_id;
            $params[HandbookManager :: PARAM_TOP_HANDBOOK_ID] = $this->top_handbook_id;


            $actions[] = new ToolbarItem(Translation :: get('EditHandbookItem'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_url($params));
        
            
        }
        //view glossary (only if this handbook has glossaries!)
        $glossary_list = HandbookManager::retrieve_all_glossaries($this->handbook_id);
        if(count($glossary_list)>0)
        {
            $params = array();
                $params[Application::PARAM_APPLICATION] = HandbookManager::APPLICATION_NAME;
                $params[HandbookManager :: PARAM_ACTION] = HandbookManager :: ACTION_VIEW_GLOSSARY;
                $params[HandbookManager :: PARAM_HANDBOOK_PUBLICATION_ID] = $this->handbook_publication_id;
                $params[HandbookManager :: PARAM_HANDBOOK_ID] = $this->handbook_id;
                $params[HandbookManager :: PARAM_HANDBOOK_SELECTION_ID] = $this->handbook_selection_id;
                $params[HandbookManager :: PARAM_COMPLEX_OBJECT_ID] = $this->complex_selection_id;
                $params[HandbookManager :: PARAM_TOP_HANDBOOK_ID] = $this->top_handbook_id;
                $preview_url = $this->get_url($params);
                $onclick = '" onclick="javascript:openPopup(\'' . $preview_url . '\'); return false;';
                $actions[] = new ToolbarItem(Translation :: get('ViewGlossary'),
                        Theme :: get_content_object_image_path(Glossary::get_type_name()),
                        $preview_url, ToolbarItem::DISPLAY_ICON_AND_LABEL, false, $onclick, '_blank');


        }
        //previous item
        if($this->previous_item_id != null)
        {
            $tool_actions[] = new ToolbarItem(Translation :: get('previous'), Theme :: get_common_image_path() . 'action_action_bar_left_hide.png', $this->get_url(array(Application::PARAM_APPLICATION => ContextLinkerManager::APPLICATION_NAME, ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => $this->previous_item_id)));
        }
        //next
        if($this->next_item_id != null)
        {
            $tool_actions[] = new ToolbarItem(Translation :: get('next'), Theme :: get_common_image_path() . 'action_action_bar_left_show.png', $this->get_url(array(Application::PARAM_APPLICATION => ContextLinkerManager::APPLICATION_NAME, ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => $this->next_item_id)));
        }

        $action_bar->set_common_actions($actions);

        $action_bar->set_tool_actions($tool_actions);

        return $action_bar;

    }





    /**
     * retrieve all the glossary's in this handbook publication and combine them in one
     * searchable table
     */
    function get_glossary()
    {

    }


    function print_metadata($co_id, $mode = self::METADATA_SHORT)
    {
        $metadata = MetadataManager::retrieve_metadata_for_content_object($co_id);
        while(list($key, $value)= each($metadata))
         {
            if($mode == self::METADATA_LONG)
            {
             $html[] = $key . ' =  '. $value . '<br/>';
            }
            else
            {
                $html[] = ' '. $value . ' ';
            }
         }
         return implode ("\n", $html);
    }

    function get_item_html($co_id, $show_alternatives_button = true)
    {
        //GET ALTERNATIVES
        $alternatives_array = HandbookManager::get_alternatives_preferences_types($co_id, $this->handbook_id);

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
        $html[] = '<div class = "handbook_item" style="float:left; width:99%;  padding:10px;">';

            $html[] = '<div class = "handbook_item_primary_info"  style="float:left;  width:'.$text_width.';">';

                //TEXT
                if($alternatives_array['text_main'] != null)
                {
                    $html[] = '<div class = "handbook_item_text" style="float:left; width:'.'100%'.';">';
                    
                    $text_tabs = new DynamicTabsRenderer('texttabs');
                    
                    $i = 0;
                    
                    $htmlt['tab'.$i][] = '<div class = "handbook_item_text" style="float:left; width:100%;">';
                    $htmlt['tab'.$i][] = '<div class="main_title">';
                    
                    $htmlt['tab'.$i][] = $alternatives_array['text_main']->get_title();
                     $htmlt['tab'.$i][] = '</div>';
                    $htmlt['tab'.$i][] = '<div class="main_text">';
                    $htmlt['tab'.$i][] = $alternatives_array['text_main']->get_text();
                    
                     $htmlt['tab'.$i][] = '</div>';
                    $htmlt['tab'.$i][] = '</div>';

                     
                    //ALTERNATIVE TEXT
                    if(count($alternatives_array['text'])>0 )
                    {
                        
                        $tab_name = $this->print_metadata($alternatives_array['text_main']->get_id());
                       $text_tabs->add_tab(new DynamicContentTab('tab'.$i, $tab_name, Theme :: get_content_object_image_path(Glossary::get_type_name()), implode("\n", $htmlt['tab'.$i])));
                        $i++;
                        
                        while(list($key, $value)= each($alternatives_array['text']))
                        {

                            if($value != $alternatives_array['text_main'])
                            {
                                $htmlt['tab'.$i][]= $this->print_metadata($value->get_id());

                                $htmlt['ctab'.$i][] = '<div class="alternative_title">';
                                 $htmlt['ctab'.$i][] = $value->get_title();
                                 $htmlt['ctab'.$i][] = '</div>';
                                 $htmlt['ctab'.$i][] = $value->get_text();
                              
                              $tab_name = $this->print_metadata($value->get_id());

                                $text_tabs->add_tab(new DynamicContentTab('tab'.$i, $tab_name, Theme :: get_content_object_image_path(Glossary::get_type_name()), implode("\n", $htmlt['ctab'.$i])));

                                $i++;
                            }
                        }
                         
                        $html[] = $text_tabs->render();

                    }
                    else
                    {
                     $html[] = implode("\n", $htmlt['tab'.$i]);
                    }
                    
                    $html[] = '</div>';
                    $html[] = '</div>';
                }


               //IMAGES
                $html[] = '<div class = "handbook_item_visual" style="float:left; width:'.$visual_width.'">';
                if($alternatives_array['image_main'] != null)
                {
                    $image_tabs = new DynamicTabsRenderer('imagetabs');
                    $i = 0;

                    $html[] = '<div class = "handbook_item_images" style="padding: 10px;">';
                    //IMAGES
                    //MAIN
                    $object = $alternatives_array['image_main'];
                    $url = Path :: get(WEB_PATH) . RepositoryManager :: get_document_downloader_url($object->get_id());
                    //TODO SHOW POPUP WITH LARGER PIC ON CLICK INSTEAD OF DOWNLOAD
                   $htmli['tab'.$i][] = '<div>';
                    $htmli['tab'.$i][] = '<a href="'.$url.'"><img style = "max-width:100%" src="'.$url.'"></a>';
                    $htmli['tab'.$i][] = '</div>';

                    
                    //ALTERNATIVES
                    if(count($alternatives_array['image'])>0)
                    {
                        $tab_name = $this->print_metadata($alternatives_array['image_main']->get_id());
                       $image_tabs->add_tab(new DynamicContentTab('tab'.$i, $tab_name, Theme :: get_content_object_image_path(Glossary::get_type_name()), implode("\n", $htmli['tab'.$i])));
                        $i++;



                       

                     while(list($key, $value)= each($alternatives_array['image']))
                         {
                             
//                             $display = ContentObjectDisplay :: factory($value);
//                             $htmli['tab'.$i][] = $display->get_description();
                         $url = Path :: get(WEB_PATH) . RepositoryManager :: get_document_downloader_url($value->get_id());
                        //TODO SHOW POPUP WITH LARGER PIC ON CLICK INSTEAD OF DOWNLOAD
                       $htmli['tab'.$i][] = '<div>';
                        $htmli['tab'.$i][] = '<a href="'.$url.'"><img style = "max-width:100%" src="'.$url.'"></a>';
                        $htmli['tab'.$i][] = '</div>';

                             $tab_name = $this->print_metadata($value->get_id());

                                $image_tabs->add_tab(new DynamicContentTab('tab'.$i, $tab_name, Theme :: get_content_object_image_path(Glossary::get_type_name()), implode("\n", $htmli['tab'.$i])));

                                $i++;
                            }

                            $html[] = $image_tabs->render();
                    }
                    else
                    {
                        $html[] = implode("\n", $htmli['tab'.$i]);
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

            $html[] = '<div class = "handbook_item_secondary_info" style="float:left; ">';
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

        return implode ("\n", $html);

    }

    function get_handbook_html($co_id, $show_alternatives_button = true)
    {
        
        //GET ALTERNATIVES

        $alternatives_array = HandbookManager::get_alternatives_preferences_types($co_id, $this->handbook_id);

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
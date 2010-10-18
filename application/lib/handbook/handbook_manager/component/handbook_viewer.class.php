<?php




/**
 * @package application.handbook.handbook.component
 */
require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../handbook_menu.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/content_object/wiki_page/complex_wiki_page.class.php';
require_once dirname(__FILE__).'/../../../context_linker/context_link.class.php';
require_once dirname(__FILE__).'/../../../context_linker/context_linker_data_manager.class.php';
require_once dirname(__FILE__).'/../../../context_linker/context_linker_manager/context_linker_manager.class.php';
require_once dirname(__FILE__).'/../../../metadata/metadata_manager/metadata_manager.class.php';
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


    private $most_suitable_text_co;
    private $most_suitable_image_co;
    private $most_suitable_video_co;

    private $alternativs_text_cos;
    private $alternative_image_cos;
    private $alternative_video_cos;
	/**
     * Runs this component and displays its output.
     */
    function run()
    {


        //GET CONTENT OBJECTS TO DISPLAY
        $this->get_content_objects();
        
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

            $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' . '"></script>';
            $html[] = '<div class="clear"></div>';

            return implode ("\n", $html);
    }

    function display_content()
    {
         if ($this->selected_object)
        {
           //TODO: first determin if there are any "context alternatives" for this content object
           //if there are alternatives: choose the most suitable one to display
             // show tabs or links for the alternatives
             $cldm = ContextLinkerDataManager::get_instance();
             $rdm = RepositoryDataManager::get_instance();
             $condition = new EqualityCondition(ContextLink :: PROPERTY_ORIGINAL_CONTENT_OBJECT_ID, $this->selected_object->get_id());
             $context_links_resultset = $cldm->retrieve_full_context_links($condition);

//             $html[] = 'alternatives: ';
//             $html[] = '<br />';
//             while ($item = $context_links_resultset->next_result()) {
//
//              $html[] =  $item[ContentObject :: PROPERTY_ID] . '  '  . $item[ContentObject :: PROPERTY_TITLE] . '  '  . $item[ContentObject :: PROPERTY_TYPE] . '  '  . $item[MetadataPropertyType :: PROPERTY_NAME]. '  '  . $item[MetadataPropertyValue :: PROPERTY_VALUE];
//              $html[] = '<br/>';
//             }
//             $html[] = '<br/>';

//             $tabs = new DynamicTabsRenderer('renderer');



             //ADD TAB FOR ORIGINAL
//             $html[] = $this->selected_object->get_icon_name() . '  ' . $this->selected_object->get_type();
//             $display = ContentObjectDisplay :: factory($this->selected_object);
//             $html[] = $display->get_full_html();
//             $tabs->add_tab(new DynamicContentTab( 'Original', Translation::get('Original'), Theme::get_common_image_path().'original.png', implode("\n", $original)));

             $alternatives = $this->determine_relevant_alternatives($context_links_resultset);

             $html[] = '<div>';
             $html[] = 'current language = ' . Translation::get_instance()->get_language();
             $html[] = '</div>';


             //SHOW TEXT
             $html[] = '<div class = "handbook_text" style="width:200px;">';
             $html[] = '<H1>TEXT</H1>';
             while(list($key, $value)= each($alternatives['text']))
             {
                $html[] = '</div>';
                 $html[] = $this->print_metadata($value->get_id());
                 $display = ContentObjectDisplay :: factory($value);
                 $html[] = $display->get_full_html();

             }
             $html[] = '</div>';

             //SHOW IMAGES
             $html[] = '<div class = "handbook_images" style="width:150px;">';
             $html[] = '<H1>IMAGE</H1>';
             while(list($key, $value)= each($alternatives['image']))
             {
                $html[] = '</div>';
                 $html[] = $this->print_metadata($value->get_id());
                 $display = ContentObjectDisplay :: factory($value);
                 $html[] = $display->get_full_html();

             }
             $html[] = '</div>';

             //SHOW VIDEO
             $html[] = '<div class = "handbook_video" style="width:50%;">';
             $html[] = '<H1>VIDEO</H1>';
             while(list($key, $value)= each($alternatives['video']))
             {
                $html[] = '</div>';
                 $html[] = $this->print_metadata($value->get_id());
                 $display = ContentObjectDisplay :: factory($value);
                 $html[] = $display->get_full_html();

             }
             $html[] = '</div>';
              $html[] = '<div class = "handbook_video" style="width:50%;">';
             $html[] = '<H1>GLOSSARY</H1>';
             $html[] = $this->get_glossary();
             $html[] = '</div>';

             //SHOW GLOSSARY

             //ADD A TAB FOR EACH CONTEXT ALTERNATIVE (DYNAMIC TABS DON'T SEEM TO WORK HERE)
//             $i=0;
//             while ($item = $context_links_resultset->next_result())
//             {
//                 $alternative_co = $rdm->retrieve_content_object($item[ContentObject :: PROPERTY_ID]);
//                 $display = ContentObjectDisplay :: factory($alternative_co);
//////                 $alternative[$i] = '<div>';
////                 $alternative[$i] = 'test';
////                 $alternative[$i] = $display_alternative[$i]->get_full_html();
//                  $html[] = '</div>';
//                  $html[] = 'icon name: ' . $alternative_co->get_icon_name();
//                  $html[] =  $item[ContentObject :: PROPERTY_ID] . '  '  . $item[ContentObject :: PROPERTY_TITLE] . '  '  . $item[ContentObject :: PROPERTY_TYPE] . '  '  . $item[MetadataPropertyType :: PROPERTY_NAME]. '  '  . $item[MetadataPropertyValue :: PROPERTY_VALUE];
//                 $html[] = $display->get_full_html();
//////                 $alternative[$i] = '</div>';
////                 $tab_title[$i] = Translation::get($item[MetadataPropertyType :: PROPERTY_NAME]) . ' : '  . $item[MetadataPropertyValue :: PROPERTY_VALUE];
////                 $tabs->add_tab(new DynamicContentTab( 'Alternative'.$i, $tab_title[$i], Theme::get_common_image_path().$item[MetadataPropertyType :: PROPERTY_NAME].'.png', implode("\n", $alternative[$i])));
//                 $i++;


//             }

            //DISPLAY TABS
//            $html[] = '<div style="clear: left;"';
//            $html[]= $tabs->render();
//            $html[]= '</div>';

                
            

        }

        return implode ("\n", $html);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
//
//        //edit handbook/item
//        $action_bar->add_common_action(new ToolbarItem(Translation :: get('EditHandbook'), Theme :: get_common_image_path() . 'content_object/portfolio.png', $this->get_create_portfolio_introduction_url(), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
//
//        //delete handbook/item
//        $action_bar->add_common_action(new ToolbarItem(Translation :: get('DELETE'), Theme :: get_common_image_path() . 'action_delete.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        //add handbook/item

        //set handbook rights

        //create alternative context version
        $actions[] = new ToolbarItem(Translation :: get('CreateContextLink'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => ContextLinkerManager::APPLICATION_NAME, ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => $this->selected_object->get_id())));
        //view glossary



        $action_bar->set_common_actions($actions);

        return $action_bar;

    }

    function determine_relevant_alternatives($context_links_resultset)
    {
        $texts = array();
        $images = array();
        $videos = array();
        $rdm = RepositoryDataManager::get_instance();

        while ($item = $context_links_resultset->next_result())
             {
                 $alternative_co = $rdm->retrieve_content_object($item[ContentObject :: PROPERTY_ID]);
                 $display = ContentObjectDisplay :: factory($alternative_co);

                 if($alternative_co->get_type() == Document::get_type_name())
                 {
                     if($alternative_co->is_image())
                     {
                        $images[] = $alternative_co;
                     }
                      else if($alternative_co->is_flash() || $alternative_co->is_video() || $alternative_co->is_audio())
                      {
                        $videos[] = $alternative_co;
                      }
                      else
                      {
                          $texts[$item[MetadataPropertyValue :: PROPERTY_VALUE]] = $alternative_co;
                      }
                    }
                    else if($alternative_co->get_type() == Youtube::get_type_name())
                    {
                        $videos[] = $alternative_co;
                    }

                    
             }

             if($this->selected_object->get_type() == Document::get_type_name())
                 {
                     if($this->selected_object->is_image())
                     {
                        $images[] = $alternative_co;
                     }
                      else if($this->selected_object->is_flash() || $this->selected_object->is_video() || $this->selected_object->is_audio())
                      {
                        $videos[] = $alternative_co;
                      }
                      else
                      {
                          $condition = new EqualityCondition(MetadataPropertyValue :: PROPERTY_CONTENT_OBJECT_ID, $this->selected_object->get_id());
                          $metadata_property_values = MetadataManager::retrieve_metadata_property_values($condition);

                          $metadata_array = array();

                          while($metadata = $metadata_property_values->next_result())
                          {
                              $metadata_array[$metadata->get_property_type_id()]= $metadata->get_value();
                          }

                           
                          $texts['original'] = $this->selected_object;
                      }
                    }
                    else if($this->selected_object->get_type() == Youtube::get_type_name())
                    {
                        $videos[] = $alternative_co;
                    }


            

                 $alternatives['text'] = $texts;
                    $alternatives['image'] = $images;
                    $alternatives['video'] = $videos;



                return $alternatives;

        //Text: Most Relevant = Language

        //Images: Most Relevant = Institution


    }

    /**
     * retrieve all the glossary's in this handbook publication and combine them to one
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

}
?>
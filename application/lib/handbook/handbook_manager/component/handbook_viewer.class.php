<?php



/**
 * @package application.handbook.handbook.component
 */
require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../handbook_menu.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/content_object/wiki_page/complex_wiki_page.class.php';
require_once dirname(__FILE__).'/../../../context_linker/context_link.class.php';
require_once dirname(__FILE__).'/../../../context_linker/context_linker_data_manager.class.php';

/**
 * Component to view a handbook and it's content
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookViewerComponent extends HandbookManager
{

    private $handbook_id;
    private $handbook_selection_id;
    private $selected_object;

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
                
//                //get complex_content_object
//                $wrapper = $rdm->retrieve_complex_content_object_item($this->selection_id);
//                //get handbook_item
//                $this->selected_object = $rdm->retrieve_content_object($wrapper->get_ref());

                $this->selected_object = $rdm->retrieve_content_object($this->handbook_selection_id);

                if ($this->selected_object && $this->selected_object->get_type() == HandbookItem::get_type_name())
                {
                    //get content object
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
             $display = ContentObjectDisplay :: factory($this->selected_object);
             $html[] = $display->get_full_html();
//             $tabs->add_tab(new DynamicContentTab( 'Original', Translation::get('Original'), Theme::get_common_image_path().'original.png', implode("\n", $original)));

             

             //ADD A TAB FOR EACH CONTEXT ALTERNATIVE (DYNAMIC TABS DON'T SEEM TO WORK
             $i=0;
             while ($item = $context_links_resultset->next_result())
             {
                 $alternative_co = $rdm->retrieve_content_object($item[ContentObject :: PROPERTY_ID]);
                 $display = ContentObjectDisplay :: factory($alternative_co);
////                 $alternative[$i] = '<div>';
//                 $alternative[$i] = 'test';
//                 $alternative[$i] = $display_alternative[$i]->get_full_html();
                  $html[] = '</div>';
                 $html[] = $display->get_full_html();
////                 $alternative[$i] = '</div>';
//                 $tab_title[$i] = Translation::get($item[MetadataPropertyType :: PROPERTY_NAME]) . ' : '  . $item[MetadataPropertyValue :: PROPERTY_VALUE];
//                 $tabs->add_tab(new DynamicContentTab( 'Alternative'.$i, $tab_title[$i], Theme::get_common_image_path().$item[MetadataPropertyType :: PROPERTY_NAME].'.png', implode("\n", $alternative[$i])));
//                 $i++;


             }

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
//        $action_bar->add_common_action(new ToolbarItem(Translation :: get('DELE'), Theme :: get_common_image_path() . 'action_delete.png', $url, ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        //add handbook/item

        //set handbook rights

        //create alternative context version

        //view glossary





        return $action_bar;

    }

}
?>
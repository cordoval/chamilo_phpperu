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
use repository\content_object\youtube\Youtube;
use repository\content_object\link\Link;
use common\libraries\Display;

require_once dirname(__FILE__) . '/../handbook_manager.class.php';
require_once dirname(__FILE__) . '/../../handbook_menu.class.php';
require_once dirname(__FILE__) . '/../../handbook_rights.class.php';

/**
 * Component to view a handbook and it's content
 * @author Nathalie Blocry
 */
abstract class HandbookManagerHandbookViewerComponent extends HandbookManager
{
    const PARAMETER_SHOW_ALTERNATIVES = 'sa';
    const ALL_ALTERNATIVES = 'aa';
    const RELEVANT_ALTERNATIVES_ONLY = 'ro';
    const SESSION_PARAMETER_PUBLICATION_ID = 'HPI';

    const METADATA_SHORT = 0;
    const METADATA_LONG = 1;

    protected $handbook_publication_id; //the id of the publication
    protected $handbook_id; //the id of the parent handbook for the selection
    protected $handbook_selection_id; //the id of the current selection
    protected $complex_selection_id; //the id of the complex content object item wrapper
    protected $top_handbook_id; //the id of the top handbook
    protected $selected_object;
    protected $user_preferences = array();
    protected $handbook_preferences = array();
    protected $next_item_id;
    protected $previous_item_id;
    protected $edit_right;
    protected $view_right;
    private $light_mode;

    function get_allowed_content_object_types()
    {
        return array(Handbook :: get_type_name());
    }

    function get_rights()
    {
        $user_id = $this->get_user_id();
        $this->handbook_publication_id = Request :: get(HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID);
        if (!$this->handbook_publication_id)
        {
            $this->check_for_uid();
        }
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


        if (!$this->handbook_publication_id)
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
        if (!$this->handbook_selection_id)
        {
            $this->handbook_selection_id = Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);
        }
        $this->complex_selection_id = Request :: get(HandbookManager::PARAM_COMPLEX_OBJECT_ID);
        if (!$this->top_handbook_id)
        {
            $this->top_handbook_id = Request :: get(HandbookManager::PARAM_TOP_HANDBOOK_ID);
        }
        if (!$this->handbook_id)
        {
            $this->handbook_id = $this->top_handbook_id;
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
        elseif ($this->handbook_id && !$this->handbook_selection_id)
        {
            $publication = HandbookDataManager :: get_instance()->retrieve_handbook_publication($this->handbook_id);
            if ($publication)
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
        if ($this->selected_object != null && $this->complex_selection_id != null)
        {
            $rdm = RepositoryDataManager::get_instance();
            $complex_content_item = $rdm->retrieve_complex_content_object_item($this->complex_selection_id);
            $display_order = $complex_content_item->get_display_order();

            $next = $display_order + 1;
            $previous = $display_order - 1;
        }
    }

    function get_menu()
    {
        $html[] = '<div id="tool_browser_left">';

        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_left">';

        $html[] = '<div id="tool_bar_hide_container" class="hide">';
        $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_left_hide.png" /></a>';
        $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_left_show.png" /></a>';
        $html[] = '</div>';

        $html[] = '<div>';
        $hpid = $this->handbook_publication_id;
        $menu = new HandbookMenu('', $this->handbook_id, $this->handbook_selection_id, $this->handbook_publication_id, $this->top_handbook_id);
        $html[] = $menu->render_as_tree();
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'libraries/resources/javascript/tool_bar.js' . '"></script>';
        $html[] = '<div class="clear"></div>';

        return implode("\n", $html);
    }

    function display_not_allowed()
    {
        $html[] = 'You are not allowed to view this handbook';
        return implode("\n", $html);
    }

    function display_preferences()
    {

//        $preferences = HandbookManager::get_preferences($this->handbook_publication_id);
//        var_dump($preferences);
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

    function display_text_content()
    {
        $this->display_preferences();
        if ($this->selected_object && $this->selected_object->get_type() == Handbook::get_type_name())
        {
            $html[] = $this->get_full_handbook_html($this->selected_object->get_id());
        }
        else if ($this->selected_object)
        {
            //SHOW ONLY THE SELECTED ITEM
            $html[] = $this->get_text_item_html($this->selected_object->get_id());
        }

        return implode("\n", $html);
    }

    function display_full_content()
    {
        $this->display_preferences();
        if ($this->selected_object && $this->selected_object->get_type() == Handbook::get_type_name())
        {
            //SHOW ALL ITEMS IN THIS HANDBOOK (one level)
            //TODO: implement
            $html[] = $this->get_handbook_html($this->selected_object->get_id());
        }
        else if ($this->selected_object)
        {
            //SHOW ONLY THE SELECTED ITEM
            $html[] = $this->get_full_item_html($this->selected_object->get_id());
        }

        return implode("\n", $html);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        if ($this->edit_right)
        {
            $actions[] = new ToolbarItem(Translation :: get('EditPublicationRights'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => self::APPLICATION_NAME, self :: PARAM_ACTION => self :: ACTION_EDIT_RIGHTS, self :: PARAM_HANDBOOK_PUBLICATION_ID => $this->handbook_publication_id)));
            $actions[] = new ToolbarItem(Translation :: get('ViewHandbookPreferences'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => self::APPLICATION_NAME, self :: PARAM_ACTION => self :: ACTION_CREATE_PREFERENCE, self :: PARAM_HANDBOOK_PUBLICATION_ID => $this->handbook_publication_id)));
            $actions[] = new ToolbarItem(Translation :: get('AddNewItemToHandbook'), Theme :: get_content_object_image_path(HandbookTopic::get_type_name()), $this->get_create_handbook_item_url($this->handbook_id, $this->top_handbook_id, $this->handbook_publication_id), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
            $actions[] = new ToolbarItem(Translation :: get('ConvertWiki'), Theme :: get_content_object_image_path(Wiki::get_type_name()), $this->get_convert_wiki_to_handbook_item_url($this->handbook_id, $this->top_handbook_id, $this->handbook_publication_id, $this->handbook_selection_id), ToolbarItem :: DISPLAY_ICON_AND_LABEL);
            $actions[] = new ToolbarItem(Translation :: get('MakeOdf'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => self::APPLICATION_NAME, self :: PARAM_ACTION => self :: ACTION_CREATE_ODF, self :: PARAM_HANDBOOK_PUBLICATION_ID => $this->handbook_publication_id, self::PARAM_HANDBOOK_ID => $this->handbook_id)));
            $actions[] = new ToolbarItem(Translation :: get('Export'), Theme :: get_common_image_path() . 'action_create.png', $this->get_url(array(Application::PARAM_APPLICATION => self::APPLICATION_NAME, self :: PARAM_ACTION => self :: ACTION_EXPORT, self :: PARAM_HANDBOOK_ID => $this->handbook_id)));

        }

        if ($this->selected_object && $this->edit_right)
        {
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
        if (count($glossary_list) > 0)
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
            $tool_actions[] = new ToolbarItem(Translation :: get('ViewGlossary'),
                            Theme :: get_content_object_image_path(Glossary::get_type_name()),
                            $preview_url, ToolbarItem::DISPLAY_ICON_AND_LABEL, false, $onclick, '_blank');
        }
        //previous item
        if ($this->previous_item_id != null)
        {
            $tool_actions[] = new ToolbarItem(Translation :: get('previous'), Theme :: get_common_image_path() . 'action_action_bar_left_hide.png', $this->get_url(array(Application::PARAM_APPLICATION => ContextLinkerManager::APPLICATION_NAME, ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => $this->previous_item_id)));
        }
        //next
        if ($this->next_item_id != null)
        {
            $tool_actions[] = new ToolbarItem(Translation :: get('next'), Theme :: get_common_image_path() . 'action_action_bar_left_show.png', $this->get_url(array(Application::PARAM_APPLICATION => ContextLinkerManager::APPLICATION_NAME, ContextLinkerManager :: PARAM_ACTION => ContextLinkerManager :: ACTION_CREATE_CONTEXT_LINK, ContextLinkerManager :: PARAM_CONTENT_OBJECT_ID => $this->next_item_id)));
        }

        //menu
        $params_menu = array();
        $params_menu[Application::PARAM_APPLICATION] = HandbookManager::APPLICATION_NAME;
        $params_menu[HandbookManager :: PARAM_ACTION] = HandbookManager :: ACTION_VIEW_HANDBOOK;
        $params_menu[HandbookManager :: ACTION_VIEW_COLLAPSED] = '1';
        $params_menu[HandbookManager :: PARAM_HANDBOOK_PUBLICATION_ID] = $this->handbook_publication_id;
        $params_menu[HandbookManager :: PARAM_HANDBOOK_ID] = $this->handbook_id;
        $params_menu[HandbookManager :: PARAM_HANDBOOK_SELECTION_ID] = $this->handbook_selection_id;
        $params_menu[HandbookManager :: PARAM_COMPLEX_OBJECT_ID] = $this->complex_selection_id;
        $params_menu[HandbookManager :: PARAM_TOP_HANDBOOK_ID] = $this->top_handbook_id;

        if ($_SESSION[HandbookManager::PARAM_MENU_STYLE] == HandbookManager::MENU_OPEN)
        {
            //show button to collapse menu
            $tool_actions[] = new ToolbarItem(Translation :: get('CollapseMenu'), Theme :: get_common_image_path() . 'action_list_remove.png', $this->get_url($params_menu));
        }
        else
        {
            //show button to unfold menu
            $tool_actions[] = new ToolbarItem(Translation :: get('UnfoldMenu'), Theme :: get_common_image_path() . 'action_list_add.png', $this->get_url($params_menu));
        }

        //search
        $search_params = array();
        $search_params[Application::PARAM_APPLICATION] = self::APPLICATION_NAME;
        $search_params[self :: PARAM_ACTION] = self:: ACTION_SEARCH;
        $search_params[self :: PARAM_TOP_HANDBOOK_ID] = $this->top_handbook_id;
        $search_params[self::PARAM_HANDBOOK_ID] = $this->handbook_id;
        if ($this->selected_object != null)
        {
            $search_params[self::PARAM_HANDBOOK_SELECTION_ID] = $this->selected_object->get_id();
        }
        $search_params[self::PARAM_HANDBOOK_PUBLICATION_ID] = $this->handbook_publication_id;
        $action_bar->set_search_url($this->get_url($search_params));
        $action_bar->set_common_actions($actions);
        $action_bar->set_tool_actions($tool_actions);
        return $action_bar;
    }

    function print_metadata($co_id, $mode = self::METADATA_SHORT)
    {
        $important_medatata = HandbookManager::get_publication_preferences_importance($this->handbook_publication_id);
        $metadata = MetadataManager::retrieve_metadata_for_content_object($co_id);
        while (list($key, $value) = each($metadata))
        {
            if ($mode == self::METADATA_LONG)
            {
                $html[] = $key . ' =  ' . $value . '<br/>';
            }
            else
            {
                if(in_array($key, $important_medatata))
                {
                    $html[] = ' ' . $value . ' ';
                }
            }
        }
        return implode("\n", $html);
    }

    /**
     * return the html to display the image alternatives for a content-item
     * @param <type> $co_id: the id of the orignal co
     * @param <type> $alternatives_array; the alternatives if they have already been retrieved
     * @param <type> $show_alternatives; boolean to determine wether the alternatives have to be shown
     * @return <type> string
     */
    function get_image_item_html($co_id, $alternatives_array = null, $show_alternatives = true)
    {
        //GET ALTERNATIVES
        if ($alternatives_array == null)
        {
            $alternatives_array = HandbookManager::get_alternatives_preferences_types($co_id, $this->handbook_id);
        }

        if ($alternatives_array['image_main'] != null)
        {
            $image_tabs = new DynamicTabsRenderer('imagetabs');
            $i = 0;

            $html[] = '<div class = "handbook_item_images" style="padding: 10px;">';
            $object = $alternatives_array['image_main'];
            $url = Path :: get(WEB_PATH) . RepositoryManager :: get_document_downloader_url($object->get_id());
            //TODO SHOW POPUP WITH LARGER PIC ON CLICK INSTEAD OF DOWNLOAD
            $htmli['tab' . $i][] = '<div>';
            $htmli['tab' . $i][] = '<a href="' . $url . '"><img style = "max-width:100%" src="' . $url . '"></a>';
            $htmli['tab' . $i][] = '</div>';


            //ALTERNATIVE IMAGES
            if (count($alternatives_array['image']) > 0 && $show_alternatives != false)
            {
                $tab_name = $this->print_metadata($alternatives_array['image_main']->get_id());
                $image_tabs->add_tab(new DynamicContentTab('tab' . $i, $tab_name, Theme :: get_content_object_image_path(Glossary::get_type_name()), implode("\n", $htmli['tab' . $i])));
                $i++;
                while (list($key, $value) = each($alternatives_array['image']))
                {
                    $url = Path :: get(WEB_PATH) . RepositoryManager :: get_document_downloader_url($value->get_id());
                    //TODO SHOW POPUP WITH LARGER PIC ON CLICK INSTEAD OF DOWNLOAD
                    $htmli['tab' . $i] = array();
                    $htmli['tab' . $i][] = '<div>';
                    $htmli['tab' . $i][] = '<a href="' . $url . '"><img style = "max-width:100%" src="' . $url . '"></a>';
                    $htmli['tab' . $i][] = '</div>';
                    $tab_name = $this->print_metadata($value->get_id());

                    $image_tabs->add_tab(new DynamicContentTab('tab' . $i, $tab_name, Theme :: get_content_object_image_path(Glossary::get_type_name()), implode("\n", $htmli['tab' . $i])));

                    $i++;
                }

                $html[] = $image_tabs->render();
            }
            else
            {
                $html[] = implode("\n", $htmli['tab' . $i]);
            }
        }
        else
        {
            $html[] = 'no image content';
        }
        return implode("\n", $html);
    }

    /**
     * return the html to display the less important alternatives (links & others) for a content-item
     * @param <type> $co_id: the id of the orignal co
     * @param <type> $alternatives_array; the alternatives if they have already been retrieved
     * @param <type> $show_alternatives; boolean to determine wether the alternatives have to be shown
     * @return <type> string
     */
    function get_secondary_item_html($co_id, $alternatives_array = null, $show_alternatives = true)
    {
        //GET ALTERNATIVES
        if ($alternatives_array == null)
        {
            $alternatives_array = HandbookManager::get_alternatives_preferences_types($co_id, $this->handbook_id);
        }

        $html[] = '<div>';
        $html[] = ' . ';
        $html[] = '</div>';

        if (count($alternatives_array['link']) > 0 || count($alternatives_array['other']) > 0)
        {
            $other_tabs = new DynamicTabsRenderer('other_tabs');
            if (count($alternatives_array['link']) > 0)
            {
                //SHOW LINKS TAB
                $html_links = array();
                while (list($key, $value) = each($alternatives_array['link']))
                {
                    $display3 = ContentObjectDisplay :: factory($value);
                    $html_links[] = $display3->get_short_html();
                    $html_links[] = '</br>';
                }
                $other_tabs->add_tab(new DynamicContentTab('link', 'links', Theme :: get_content_object_image_path(Link::get_type_name()), implode("\n", $html_links)));
            }
            if (count($alternatives_array['other']) > 0)
            {
                //SHOW OTHERS
                while (list($key, $value) = each($alternatives_array['other']))
                {
                    $display = ContentObjectDisplay :: factory($value);

                    $html_others[] = $display->get_full_html();
                    $html_others[] = '</div>';
                }
                $other_tabs->add_tab(new DynamicContentTab('others', 'others', Theme :: get_content_object_image_path(Link::get_type_name()), implode("\n", $html_others)));
            }
            $html[] = $other_tabs->render();
            $html[] = '</div>';
        }
        else
        {
            $html[] = 'no secondary content';
        }
        return implode("\n", $html);
    }

    /**
     * return the html to display the video alternatives for a content-item
     * @param <type> $co_id: the id of the orignal co
     * @param <type> $alternatives_array; the alternatives if they have already been retrieved
     * @param <type> $show_alternatives; boolean to determine wether the alternatives have to be shown
     * @return <type> string
     */
    function get_video_item_html($co_id, $alternatives_array = null, $show_alternatives = true)
    {
        //GET ALTERNATIVES
        if ($alternatives_array == null)
        {
            $alternatives_array = HandbookManager::get_alternatives_preferences_types($co_id, $this->handbook_id);
        }
        //VIDEO
        if ($alternatives_array['video_main'] != null)
        {
            $video_tabs = new DynamicTabsRenderer('videotabs');
            $i = 0;
            $html[] = '<div class = "handbook_item_videos" style="padding: 10px;">';
            $object = $alternatives_array['video_main'];
            $display = ContentObjectDisplay :: factory($alternatives_array['video_main']);
            $htmlv['tab' . $i][] = $display->get_preview(true);
            $htmlv['tab' . $i][] = '</div>';

            //ALTERNATIVE VIDEO
            if (count($alternatives_array['video']) > 0 && $show_alternatives != true)
            {
                $tab_name = $this->print_metadata($alternatives_array['video_main']->get_id());
                $video_tabs->add_tab(new DynamicContentTab('tab' . $i, $tab_name, Theme :: get_content_object_image_path(Glossary::get_type_name()), implode("\n", $htmlv['tab' . $i])));
                $i++;
                while (list($key, $value) = each($alternatives_array['video']))
                {
                    $display2 = ContentObjectDisplay :: factory($value);
                    $htmlv['tab' . $i][] = $display2->get_preview(true);
                    $tab_name = $this->print_metadata($value->get_id());
                    $video_tabs->add_tab(new DynamicContentTab('tab' . $i, $tab_name, Theme :: get_content_object_image_path(Youtube::get_type_name()), implode("\n", $htmlv['tab' . $i])));
                    $i++;
                }
                $html[] = $video_tabs->render();
            }
            else
            {
                $html[] = implode("\n", $htmlv['tab' . $i]);
            }
            $html[] = '</div>';
        }
        else
        {
            $html[] = 'no video content';
        }

        return implode("\n", $html);
    }

    /**
     * return the html to display the textual alternatives for a content-item
     * @param <type> $co_id: the id of the orignal co
     * @param <type> $alternatives_array; the alternatives if they have already been retrieved
     * @param <type> $show_alternatives; boolean to determine wether the alternatives have to be shown
     * @return <type> string
     */
    function get_text_item_html($co_id, $alternatives_array = null, $text_width = 100, $show_alternatives = true)
    {
        //GET ALTERNATIVES
        if ($alternatives_array == null)
        {
            $alternatives_array = HandbookManager::get_alternatives_preferences_types($co_id, $this->handbook_id);
        }
        //TEXT
        if ($alternatives_array['text_main'] != null)
        {
            $text_tabs = new DynamicTabsRenderer('texttabs');
            $i = 0;

            $htmlt['tab' . $i][] = '<div class = "handbook_topic_title">';
            $htmlt['tab' . $i][] = $alternatives_array['text_main']->get_title();
            $htmlt['tab' . $i][] = '</div>';

            $htmlt['tab' . $i][] = '<div class = "handbook_topic_text">';
            $htmlt['tab' . $i][] = $alternatives_array['text_main']->get_text();
            $htmlt['tab' . $i][] = '</div>';


            //ALTERNATIVE TEXT
            if (count($alternatives_array['text']) > 0 && $show_alternatives != false)
            {
                $tab_name = $this->print_metadata($alternatives_array['text_main']->get_id());
                $text_tabs->add_tab(new DynamicContentTab('tab' . $i, $tab_name, Theme :: get_content_object_image_path(Glossary::get_type_name()), implode("\n", $htmlt['tab' . $i])));
                $i++;

                while (list($key, $value) = each($alternatives_array['text']))
                {
                    if ($value != $alternatives_array['text_main'])
                    {
                        $htmlt['ctab' . $i][] = '<div class = "handbook_topic_title">';
                        $htmlt['ctab' . $i][] = $value->get_title();
                        $htmlt['ctab' . $i][] = '</div>';

                        $htmlt['ctab' . $i][] = '<div class = "handbook_topic_text">';
                        $htmlt['ctab' . $i][] = $value->get_text();
                        $htmlt['ctab' . $i][] = '</div>';

                        $tab_name = $this->print_metadata($value->get_id());
                        $text_tabs->add_tab(new DynamicContentTab('tab' . $i, $tab_name, Theme :: get_content_object_image_path(Glossary::get_type_name()), implode("\n", $htmlt['ctab' . $i])));
                        $i++;
                    }
                }

                $html[] = $text_tabs->render();
            }
            else
            {
                $html[] = implode("\n", $htmlt['tab' . $i]);
            }
        }
        else
        {
//            $html[] = 'no text content';
        }

        return implode("\n", $html);
    }

    function get_full_item_html($co_id, $show_alternatives = true)
    {
        //GET ALTERNATIVES
        $alternatives_array = HandbookManager::get_alternatives_preferences_types($co_id, $this->handbook_id);

        //DETERMINE PAGE LAYOUT
        $text_width;
        $visual_width;
        if (($alternatives_array['image_main'] != null || $alternatives_array['video_main'] != null) && $this->light_mode != 1)
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
        $html[] = '<div class = "handbook_item_primary_info"  style="float:left;   width:99%;">';

        //TEXT
        $html[] = '<div class = "handbook_item_text" style="float:left; width:' . $text_width . ';">';
        $html[] = $this->get_text_item_html($co_id, $alternatives_array, $text_width);
        $html[] = '</div>'; //close text
        //IMAGES & VIDEO
        if ($alternatives_array['video_main'] != null || $alternatives_array['image_main'] != null)
        {
            $html[] = '<div class = "handbook_item_visual" style="float:left; width:' . $visual_width . '">';
            if ($alternatives_array['image_main'] != null)
            {
                $html[] = $this->get_image_item_html($co_id, $alternatives_array);
            }
            if ($alternatives_array['video_main'] != null)
            {
                $html[] = $this->get_video_item_html($co_id, $alternatives_array);
            }
            $html[] = '</div>'; //close visual info
        }

        $html[] = '</div>'; //close primary info
        //OTHER
        if (count($alternatives_array['link']) > 0 || count($alternatives_array['other']) > 0)
        {
            $html[] = '<div class = "handbook_item_secondary_info" style="width:79%;">';
            $html[] = $this->get_secondary_item_html($co_id, $alternatives_array);
            $html[] = '</div>'; //close secondary info
        }
        $html[] = '</div>'; //close handbook item

        return implode("\n", $html);
    }

    function get_handbook_html($co_id, $show_alternatives = true)
    {
        //GET ALTERNATIVES
        $alternatives_array = HandbookManager::get_alternatives_preferences_types($co_id, $this->handbook_id);
        //DISPLAY TITLES
        //todo: implement';
        $html[] = '<H1>' . $alternatives_array['handbook_main']->get_title() . '</H1>';
        $html[] = $alternatives_array['handbook_main']->get_description();
        $html[] = 'alternative titles: ';
        while (list($key, $value) = each($alternatives_array['handbook']))
        {
            $html[] = '  -  ';
            $html[] = $value->get_title();
        }
        return implode("\n", $html);
    }

    function display_header()
    {
        parent::display_header();
    }

    function display_footer()
    {
        parent::display_footer();
    }

}

?>
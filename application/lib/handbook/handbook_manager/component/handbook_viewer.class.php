<?php

/**
 * @package application.handbook.handbook.component
 */
require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../handbook_menu.class.php';
require_once dirname(__FILE__).'/../../../../../repository/lib/content_object/wiki_page/complex_wiki_page.class.php';



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
           
                               //display information on the portfolio publication
                $display = ContentObjectDisplay :: factory($this->selected_object);
                $html[] = $display->get_full_html();
            

        }

        return implode ("\n", $html);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        return $action_bar;

    }

}
?>
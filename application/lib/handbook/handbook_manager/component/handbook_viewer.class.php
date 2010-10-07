<?php

/**
 * @package application.handbook.handbook.component
 */
require_once dirname(__FILE__).'/../handbook_manager.class.php';
require_once dirname(__FILE__).'/../../handbook_menu.class.php';


/**
 * Component to view a handbook and it's content
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookViewerComponent extends HandbookManager
{

    private $handbook_id;

	/**
     * Runs this component and displays its output.
     */
    function run()
    {
        $this->handbook_id = Request :: get(HandbookManager::PARAM_HANDBOOK_ID);

        parent::display_header();




//        $html[] = '<div id="tool_bar" class="tool_bar tool_bar_right">';
//
//            $html[] = '<div id="tool_bar_hide_container" class="hide">';
//            $html[] = '<a id="tool_bar_hide" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_right_hide.png" /></a>';
//            $html[] = '<a id="tool_bar_show" href="#"><img src="' . Theme :: get_common_image_path() . 'action_action_bar_right_show.png" /></a>';
//            $html[] = '</div>';
//
//
//                    $html[] = '<div>';
//                    $menu = new HandbookMenu( 'run.php?go='.self::ACTION_VIEW_HANDBOOK.'&application=handbook&'. HandbookManager::PARAM_HANDBOOK_ID.'='.$this->handbook_id, $this->handbook_id);
//                    $html[] = $menu->render_as_tree();
//                    $html[] = '</div>';
//
//
//        $html[] = '</div>';
//        $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/tool_bar.js' . '"></script>';
//        $html[] = '<div class="clear"></div>';
//
        
        $html[] ='<div id="tool_browser_left">';
            //MENU
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


            //CONTENT
            $html[] = '<div>';
            $html[] = 'INHOUD KOMT HIER';
            $html[] = '</div>';

        $html[] = '</div>';


        echo implode ("\n", $html);
        parent::display_footer();
    }

    function get_allowed_content_object_types()
    {
        return array(Handbook :: get_type_name());
    }
}
?>
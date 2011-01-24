<?php

namespace application\handbook;

use common\libraries\Request;
use common\libraries\BreadcrumbTrail;

require_once dirname(__FILE__) . '/handbook_viewer.class.php';
/**
 * Component to view a handbook and it's full content
 * @author Nathalie Blocry
 */
class HandbookManagerHandbookFullViewerComponent extends HandbookManagerHandbookViewerComponent
{
    

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        //TOGGLE MENU REQUIRED?
        if (Request::get(HandbookManager::ACTION_VIEW_COLLAPSED) == '1')
        {
            //yes
            if ($_SESSION[HandbookManager::PARAM_MENU_STYLE] == HandbookManager::MENU_OPEN)
            {
                $_SESSION[HandbookManager::PARAM_MENU_STYLE] = HandbookManager::MENU_COMPACT;
            }
            else
            {
                $_SESSION[HandbookManager::PARAM_MENU_STYLE] = HandbookManager::MENU_OPEN;
            }
        }

        //GET CONTENT OBJECTS TO DISPLAY
        $this->get_rights();

        if ($this->view_right)
        {
            $this->get_content_objects();
            $this->get_preferences($this->handbook_id);

            $this->display_header();

            
                //ACTIONBAR
                $this->action_bar = $this->get_action_bar();
                $html[] = $this->action_bar->as_html();
           

                //MENU
                $html[] = $this->get_menu();
            
            //CONTENT
            $html[] = '<div>';
            $html[] = $this->display_full_content();
            $html[] = '</div>';

            $html[] = '</div>';
            $html[] = '</div>';

            echo implode("\n", $html);
            $this->display_footer();
        }
        else
        {
            $this->display_header();
            $html[] = '<div>';
            $html[] = $this->display_not_allowed();
            $html[] = '</div>';
            echo implode("\n", $html);
           $this->display_footer();
        }
    }

   
    function display_header()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add_help('handbook viewer');
        parent::display_header();

    }

    function display_footer()
    {
        parent::display_footer();

    }

}

?>
<?php

namespace application\alexia;

use common\libraries\Request;
use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Theme;
use repository\ContentObjectDisplay;
/**
 * $Id: viewer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.alexia_manager
 */

class AlexiaManagerViewerComponent extends AlexiaManager
{
    private $folder;
    private $publication;
    private $actionbar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(AlexiaManager :: PARAM_ALEXIA_ID);
        
        if (isset($id))
        {
            $this->publication = $this->retrieve_alexia_publication($id);
            $publication = $this->publication;
            
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AlexiaManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Alexia')));
            $trail->add(new Breadcrumb($this->get_url(), $publication->get_publication_object()->get_title()));
            $trail->add_help('alexia general');
            
            $this->action_bar = $this->get_action_bar($publication);
            
            $this->display_header($trail);
            echo $this->action_bar->as_html();
            echo '<div class="clear"></div><br />';
            echo $this->get_publication_as_html();
            
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected', null, Utilities::COMMON_LIBRARIES)));
        }
    }

    function get_action_bar($publication)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_edit.png', $this->get_publication_editing_url($publication), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete', null, Utilities::COMMON_LIBRARIES), Theme :: get_common_image_path() . 'action_delete.png', $this->get_publication_deleting_url($publication), ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));
        
        return $action_bar;
    }

    function get_publication_as_html()
    {
        $publication = $this->publication;
        $link = $publication->get_publication_object();
        $html = array();
        
        $display = ContentObjectDisplay :: factory($link);
        $html[] = $display->get_full_html();
        
        return implode("\n", $html);
    }
}
?>
<?php
/**
 * $Id: viewer.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenberg.gutenberg_manager
 */
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'gutenberg_manager/gutenberg_manager.class.php';
require_once Path :: get_library_path() . 'utilities.class.php';

class GutenbergManagerViewerComponent extends GutenbergManager
{
    private $publication;
    private $actionbar;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(GutenbergManager :: PARAM_GUTENBERG_ID);
        
        if (isset($id))
        {
            $this->publication = $this->retrieve_gutenberg_publication($id);
            $publication = $this->publication;
            
            $trail = BreadcrumbTrail :: get_instance();
            $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => GutenbergManager :: ACTION_BROWSE_PUBLICATIONS)), Translation :: get('Gutenberg')));
            $trail->add(new Breadcrumb($this->get_url(), $publication->get_publication_object()->get_title()));
            $trail->add_help('gutenberg general');
            
            $this->action_bar = $this->get_action_bar($publication);
            
            $this->display_header($trail);
            if ($this->get_user()->is_platform_admin())
            {
                echo $this->action_bar->as_html();
                echo '<div class="clear"></div><br />';
            }
            echo $this->get_publication_as_html();
            
            $this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoPublicationSelected')));
        }
    }

    function get_action_bar($publication)
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_publication_editing_url($publication), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_publication_deleting_url($publication), ToolbarItem :: DISPLAY_ICON_AND_LABEL, true));
        
        return $action_bar;
    }

    function get_publication_as_html()
    {
        $publication = $this->publication;
        $comic_book = $publication->get_publication_object();
        $html = array();
        
        $display = ContentObjectDisplay :: factory($comic_book);
        $html[] = $display->get_full_html();
        
        return implode("\n", $html);
    }
}
?>
<?php

/**
 * Cell renderer for the learning object browser table
 */
class PhotoGalleryPublicationBrowserTableCellRenderer extends DefaultPhotoGalleryTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param PhotoGalleryManagerBrowserComponent $browser
     */
    function PhotoGalleryPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $photo_gallery)
    {
        if ($column === PhotoGalleryPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($photo_gallery);
        }
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $photo_gallery);
                $title_short = Utilities :: truncate_string($title, 53, false);
                return '<a href="' . htmlentities($photo_gallery->get_publication_object()->get_url()) . '" title="' . $title . '">' . $title_short . '</a>';
        }
        
        return parent :: render_cell($column, $photo_gallery);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $photo_gallery The photo_gallery object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($photo_gallery)
    {
        $toolbar = new Toolbar();
        $toolbar->add_items($this->browser->get_photo_gallery_actions($photo_gallery));
        return $toolbar->as_html();
        
    	$toolbar = new Toolbar(); 
        
        $viewing_url = $this->browser->get_publication_viewing_url($photo_gallery);
        
        $toolbar->add_item(new ToolbarItem(Translation :: get('View'), Theme :: get_common_image_path() . 'action_details.png', $viewing_url, ToolbarItem :: DISPLAY_ICON));
        
//        if ($this->browser->get_user()->is_platform_admin() || $photo_gallery->get_publisher() == $this->browser->get_user()->get_id())
//        {
//            $edit_url = $this->browser->get_publication_editing_url($photo_gallery);
//            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $edit_url, ToolbarItem :: DISPLAY_ICON));
//            
//            $delete_url = $this->browser->get_publication_deleting_url($photo_gallery);
//            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $delete_url, ToolbarItem :: DISPLAY_ICON, true));
//        }
        
        return $toolbar->as_html();
    }
}
?>
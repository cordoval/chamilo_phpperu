<?php
/**
 * $Id: system_announcement_publication_browser_table_cell_renderer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component.system_announcement_publication_table
 */
require_once dirname(__FILE__) . '/system_announcement_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../system_announcement_publication_table/default_system_announcement_publication_table_cell_renderer.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class SystemAnnouncementPublicationBrowserTableCellRenderer extends DefaultSystemAnnouncementPublicationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ProfileManagerBrowserComponent $browser
     */
    function SystemAnnouncementPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $system_announcement_publication)
    {
        if ($column === SystemAnnouncementPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($system_announcement_publication);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case SystemAnnouncementPublication :: PROPERTY_PUBLISHED :
                return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $system_announcement_publication->get_published());
                break;
            case SystemAnnouncementPublication :: PROPERTY_CONTENT_OBJECT_ID :
                $title = parent :: render_cell($column, $system_announcement_publication);
                $title_short = $title;
                //				if(strlen($title_short) > 53)
                //				{
                //					$title_short = mb_substr($title_short,0,50).'&hellip;';
                //				}
                $title_short = Utilities :: truncate_string($title_short, 53, false);
                return '<a href="' . htmlentities($this->browser->get_system_announcement_publication_viewing_url($system_announcement_publication)) . '" title="' . $title . '">' . $title_short . '</a>';
                break;
        }
        return parent :: render_cell($column, $system_announcement_publication);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $profile The profile object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($system_announcement_publication)
    {
        $toolbar_data = array();
        
        if ($this->browser->get_user()->is_platform_admin() || $system_announcement_publication->get_publisher() == $this->browser->get_user()->get_id())
        {
            $edit_url = $this->browser->get_system_announcement_publication_editing_url($system_announcement_publication);
            $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            
            $delete_url = $this->browser->get_system_announcement_publication_deleting_url($system_announcement_publication);
            $toolbar_data[] = array('href' => $delete_url, 'label' => Translation :: get('Delete'), 'confirm' => true, 'img' => Theme :: get_common_image_path() . 'action_delete.png');
            
            $visibility_url = $this->browser->get_system_announcement_publication_visibility_url($system_announcement_publication);
            if ($system_announcement_publication->is_hidden())
            {
                $visibility_img = 'action_invisible.png';
            }
            elseif ($system_announcement_publication->is_forever())
            {
                $visibility_img = 'action_visible.png';
            }
            else
            {
                $visibility_img = 'action_period.png';
            }
            
            $toolbar_data[] = array('href' => $visibility_url, 'label' => Translation :: get('Hide'), 'img' => Theme :: get_common_image_path() . $visibility_img);
        
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
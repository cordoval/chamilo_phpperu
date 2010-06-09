<?php
/**
 * $Id: profile_publication_browser_table_cell_renderer.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler.profiler_manager.component.profile_publication_browser
 */
require_once dirname(__FILE__) . '/profile_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../profile_publication_table/default_profile_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../profiler_manager.class.php';
/**
 * Cell renderer for the learning object browser table
 */
class ProfilePublicationBrowserTableCellRenderer extends DefaultProfilePublicationTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ProfileManagerBrowserComponent $browser
     */
    function ProfilePublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $profile)
    {
        if ($column === ProfilePublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($profile);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case ProfilePublication :: PROPERTY_PUBLISHED :
                return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $profile->get_published());
                break;
            case ProfilePublication :: PROPERTY_PROFILE :
                $title = parent :: render_cell($column, $profile);
                $title_short = $title;
                //				if(strlen($title_short) > 53)
                //				{
                //					$title_short = mb_substr($title_short,0,50).'&hellip;';
                //				}
                $title_short = Utilities :: truncate_string($title_short, 53, false);
                return '<a href="' . htmlentities($this->browser->get_publication_viewing_url($profile)) . '" title="' . $title . '">' . $title_short . '</a>';
                break;
        }
        return parent :: render_cell($column, $profile);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $profile The profile object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($profile)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
    	if ($this->browser->get_user()->is_platform_admin() || $profile->get_publisher() == $this->browser->get_user()->get_id())
        {
            $edit_url = $this->browser->get_publication_editing_url($profile);
            $delete_url = $this->browser->get_publication_deleting_url($profile);
            
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'),
        			Theme :: get_common_image_path() . 'action_edit.png',
        			$edit_url,
        			ToolbarItem :: DISPLAY_ICON
	        ));
	        
	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Delete'),
	        		Theme :: get_common_image_path() . 'action_delete.png',
	        		$delete_url,
	        		ToolbarItem :: DISPLAY_ICON,
	        		true
	        ));
        }
        
        return $toolbar->as_html();
    }
}
?>
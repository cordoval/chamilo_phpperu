<?php
/**
 * $Id: repository_shared_content_objects_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser.shared_content_objects_browser
 */
require_once dirname(__FILE__) . '/repository_shared_content_objects_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../../content_object_table/default_shared_content_objects_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RepositorySharedContentObjectsBrowserTableCellRenderer extends DefaultSharedContentObjectsTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function RepositorySharedContentObjectsBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === RepositorySharedContentObjectsBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($content_object);
        }
        
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TYPE :
                return '<a href="' . htmlentities($this->browser->get_type_filter_url($content_object->get_type())) . '">' . parent :: render_cell($column, $content_object) . '</a>';
            case ContentObject :: PROPERTY_TITLE :
                $title = parent :: render_cell($column, $content_object);
                $title_short = Utilities :: truncate_string($title, 53, false);
                if ($this->browser->has_right($content_object->get_id(), RepositoryRights :: VIEW_RIGHT))
                	return '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($content_object)) . '" title="' . $title . '">' . $title_short . '</a>';
                else
                	return $title_short;
            case ContentObject :: PROPERTY_MODIFICATION_DATE :
                return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $content_object->get_modification_date());
            case ContentObject :: PROPERTY_OWNER_ID :
                return UserDataManager :: get_instance()->retrieve_user($content_object->get_owner_id())->get_fullname();
        }
        return parent :: render_cell($column, $content_object);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($content_object)
    {
        $toolbar = new Toolbar();
        
        if ($this->browser->has_right($content_object->get_id(), RepositoryRights :: VIEW_RIGHT))
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('View'),
        			Theme :: get_common_image_path().'action_visible.png', 
					$this->browser->get_content_object_viewing_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
			));
        else
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('View'),
        			Theme :: get_common_image_path().'action_visible_na.png', 
					null,
				 	ToolbarItem :: DISPLAY_ICON
			));
        if ($this->browser->has_right($content_object->get_id(), RepositoryRights :: USE_RIGHT))
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Publish'),
        			Theme :: get_common_image_path().'action_publish.png', 
					$this->browser->get_publish_content_object_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
			));
        else
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('PublishNA'),
        			Theme :: get_common_image_path().'action_publish_na.png', 
					null,
				 	ToolbarItem :: DISPLAY_ICON
			));
        if ($this->browser->has_right($content_object->get_id(), RepositoryRights :: REUSE_RIGHT))
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('ReUse'),
        			Theme :: get_common_image_path().'action_reuse.png', 
					$this->browser->get_copy_content_object_url($content_object->get_id(), $this->browser->get_user_id()),
				 	ToolbarItem :: DISPLAY_ICON
			));
        else
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('ReUseNA'),
        			Theme :: get_common_image_path().'action_reuse_na.png', 
					null,
				 	ToolbarItem :: DISPLAY_ICON
			));
        
        if ($content_object->is_complex_content_object())
        {
        	            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('BrowseComplex'),
        			Theme :: get_common_image_path().'action_browser.png', 
					$this->browser->get_browse_complex_content_object_url($content_object),
				 	ToolbarItem :: DISPLAY_ICON
			));
        }
        
        return $toolbar->as_html();
    }
}
?>
<?php
/**
 * $Id: repository_browser_table_cell_renderer.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.repository_manager.component.browser
 */
require_once dirname(__FILE__) . '/repository_version_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../content_object_table/default_content_object_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RepositoryVersionBrowserTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function RepositoryVersionBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $content_object)
    {
        if ($column === RepositoryVersionBrowserTableColumnModel :: get_modification_column())
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
                return '<a href="' . htmlentities($this->browser->get_content_object_viewing_url($content_object)) . '" title="' . $title . '">' . $title_short . '</a>';
            case Translation :: get('CompareWith') :
                return HTML_QuickForm :: createElement('radio', 'B', null, null, 'B' . $content_object->get_id())->toHtml();
//                return 'aa';
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
        
        $toolbar->add_item(new ToolbarItem(
    			Translation :: get('Delete'),
    			Theme :: get_common_image_path().'action_delete.png', 
				$this->browser->get_content_object_moving_url($content_object),
			 	ToolbarItem :: DISPLAY_ICON
		));
		
		$toolbar->add_item(new ToolbarItem(
    			Translation :: get('Revert'),
    			Theme :: get_common_image_path().'action_revert.png', 
				$this->browser->get_content_object_metadata_editing_url($content_object),
			 	ToolbarItem :: DISPLAY_ICON
		));
		
        return $toolbar->as_html();
    }
}
?>
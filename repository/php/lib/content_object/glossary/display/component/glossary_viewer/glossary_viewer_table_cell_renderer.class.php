<?php
/**
 * $Id: glossary_viewer_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.glossary.component.glossary_viewer
 */
require_once Path :: get_repository_path() . 'lib/content_object_table/default_content_object_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/glossary_viewer_table_column_model.class.php';
/**
 * This class is a cell renderer for a publication candidate table
 */
class GlossaryViewerTableCellRenderer extends DefaultContentObjectTableCellRenderer
{
    private $table_actions;
    private $browser;
    private $dm;
    private $glossary_item;

    /**
     * Constructor.
     * @param string $publish_url_format URL for publishing the selected
     * learning object.
     * @param string $edit_and_publish_url_format URL for editing and publishing
     * the selected learning object.
     */
    function GlossaryViewerTableCellRenderer($browser)
    {
        $this->table_actions = array();
        $this->browser = $browser;
        $this->dm = RepositoryDataManager :: get_instance();
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $glossary_item)
    {
        if ($column === GlossaryViewerTableColumnModel :: get_action_column())
        {
            return $this->get_actions($glossary_item);
        }
        
        if (! $this->glossary_item || $this->glossary_item->get_id() != $glossary_item->get_ref())
            $this->glossary_item = $this->dm->retrieve_content_object($glossary_item->get_ref(), GlossaryItem :: get_type_name());
        
        switch ($column->get_name())
        {
            case GlossaryItem :: PROPERTY_TITLE :
                return $this->glossary_item->get_title();
            case GlossaryItem :: PROPERTY_DESCRIPTION :
                return $this->glossary_item->get_description();
        }
    }

    function get_actions($glossary_item)
    {
    	$toolbar = new Toolbar();
    	
        if ($this->browser->is_allowed(EDIT_RIGHT))
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'),
        			Theme :: get_common_image_path().'action_edit.png', 
					$this->browser->get_complex_content_object_item_update_url($glossary_item),
					ToolbarItem :: DISPLAY_ICON
			));
        }
        
        if ($this->browser->is_allowed(DELETE_RIGHT))
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Delete'),
        			Theme :: get_common_image_path().'action_delete.png', 
					$this->browser->get_complex_content_object_item_delete_url($glossary_item),
					ToolbarItem :: DISPLAY_ICON,
					true
			));
        }
        
        return $toolbar->as_html();
    }

}
?>
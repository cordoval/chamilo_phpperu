<?php
/**
 * $Id: type_template_browser_table_cell_renderer.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.type_template_manager.component.type_template_browser_table
 */
require_once dirname(__FILE__) . '/type_template_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/type_template_table/default_type_template_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class TypeTemplateBrowserTableCellRenderer extends DefaultTypeTemplateTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function TypeTemplateBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $type_template)
    {
        if ($column === TypeTemplateBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($type_template);
        }
        
        return parent :: render_cell($column, $type_template);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($type_template)
    {
        $toolbar = new Toolbar();
        
		$toolbar->add_item(new ToolbarItem(
      		Translation :: get('ManageTypeTemplates'),
        	Theme :: get_common_image_path().'action_rights.png', 
			$this->browser->get_manage_type_template_rights_url($type_template),
		 	ToolbarItem :: DISPLAY_ICON
		));
			
       	$toolbar->add_item(new ToolbarItem(
   			Translation :: get('Edit'),
   			Theme :: get_common_image_path().'action_edit.png', 
			$this->browser->get_type_template_editing_url($type_template),
		 	ToolbarItem :: DISPLAY_ICON
		));
			
	  	$toolbar->add_item(new ToolbarItem(
        	Translation :: get('Delete'),
        	Theme :: get_common_image_path().'action_delete.png', 
			$this->browser->get_type_template_deleting_url($type_template),
		 	ToolbarItem :: DISPLAY_ICON,
		 	true
		));
		
        return $toolbar->as_html();
    }
}
?>
<?php
/**
 * $Id: rights_template_browser_table_cell_renderer.class.php 214 2009-11-13 13:57:37Z vanpouckesven $
 * @package rights.lib.rights_template_manager.component.rights_template_browser_table
 */
require_once dirname(__FILE__) . '/rights_template_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/rights_template_table/default_rights_template_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class RightsTemplateBrowserTableCellRenderer extends DefaultRightsTemplateTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function RightsTemplateBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $rights_template)
    {
        if ($column === RightsTemplateBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($rights_template);
        }
        
        return parent :: render_cell($column, $rights_template);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($rights_template)
    {
        $toolbar = new Toolbar();
        
		$toolbar->add_item(new ToolbarItem(
      		Translation :: get('ManageRightsTemplates'),
        	Theme :: get_common_image_path().'action_rights.png', 
			$this->browser->get_manage_rights_template_rights_url($rights_template),
		 	ToolbarItem :: DISPLAY_ICON
		));
			
       	$toolbar->add_item(new ToolbarItem(
   			Translation :: get('Edit'),
   			Theme :: get_common_image_path().'action_rights.png', 
			$this->browser->get_rights_template_editing_url($rights_template),
		 	ToolbarItem :: DISPLAY_ICON
		));
			
	  	$toolbar->add_item(new ToolbarItem(
        	Translation :: get('Delete'),
        	Theme :: get_common_image_path().'action_delete.png', 
			$this->browser->get_rights_template_deleting_url($rights_template),
		 	ToolbarItem :: DISPLAY_ICON,
		 	true
		));
		
        return $toolbar->as_html();
    }
}
?>
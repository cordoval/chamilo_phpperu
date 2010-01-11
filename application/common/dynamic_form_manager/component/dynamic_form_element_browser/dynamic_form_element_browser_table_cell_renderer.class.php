<?php
/**
 * $Id: dynamic_form_element_browser_table_cell_renderer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package application.common.dynamic_form_manager.component.dynamic_form_element_browser
 * @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/dynamic_form_element_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../dynamic_form_element_table/default_dynamic_form_element_table_cell_renderer.class.php';
/**
 * Cell renderer for the user object browser table
 */
class DynamicFormElementBrowserTableCellRenderer extends DefaultDynamicFormElementTableCellRenderer
{
    /**
     * The user browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function DynamicFormElementBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $dynamic_form_element)
    {
        if ($column === DynamicFormElementBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($dynamic_form_element);
        }
        
        return parent :: render_cell($column, $dynamic_form_element);
    }

    /**
     * Gets the action links to display
     * @param $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($dynamic_form_element)
    {
        $toolbar_data = array();
        
        $update_url = $this->browser->get_update_element_url($dynamic_form_element);
        $delete_url = $this->browser->get_delete_element_url($dynamic_form_element);
        
        $toolbar_data[] = array('href' => $update_url, 
        						'label' => Translation :: get('Edit'), 
        						'img' => Theme :: get_common_image_path() . 'action_edit.png');
        
        $toolbar_data[] = array('href' => $delete_url, 
        						'label' => Translation :: get('Delete'), 
        						'img' => Theme :: get_common_image_path() . 'action_delete.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
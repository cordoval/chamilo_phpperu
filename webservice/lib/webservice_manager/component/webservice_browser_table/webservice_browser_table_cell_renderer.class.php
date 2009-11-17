<?php
/**
 * $Id: webservice_browser_table_cell_renderer.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib.webservice_manager.component.webservice_browser_table
 */
require_once dirname(__FILE__) . '/webservice_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../webservice_table/default_webservice_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class WebserviceBrowserTableCellRenderer extends DefaultWebserviceTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function WebserviceBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $webservice)
    {
        if ($column === WebserviceBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($webservice);
        }
        
        return parent :: render_cell($column, $webservice);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($webservice)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_manage_roles_url($webservice), 'label' => Translation :: get('ManageWebservices'), 'img' => Theme :: get_common_image_path() . 'action_rights.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
<?php
/**
 * $Id: user_browser_table_cell_renderer.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.portfolio.portfolio_manager.component.user_browser
 */
require_once dirname(__FILE__).'/metadata_namespace_browser_table_column_model.class.php';
require_once dirname(__FILE__).'/../../../tables/metadata_namespace_table/default_metadata_namespace_table_cell_renderer.class.php';
require_once dirname(__FILE__).'/../../../metadata_namespace.class.php';
require_once dirname(__FILE__).'/../../metadata_manager.class.php';

/**
 * Cell renderer for the user object browser table
 */
class MetadataNamespaceBrowserTableCellRenderer extends DefaultMetadataNamespaceTableCellRenderer
{
    /**
     * The user browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function MetadataNamespaceBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $namespace)
    {
        if ($column === MetadataNamespaceBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($namespace);
        }
        
        // Add special features here
        switch ($column->get_name())
        {
            case MetadataNamespace :: PROPERTY_NS_PREFIX :
                return $namespace->get_ns_prefix();
            case MetadataNamespace :: PROPERTY_NAME :
                return $namespace->get_name();
            case MetadataNamespace :: PROPERTY_URL :
                return '<a href="' . $namespace->get_url() . '">' . $namespace->get_url() . '</a>';
        }
        return parent :: render_cell($column, $namespace);
    }

    /**
     * Gets the action links to display
     * @param $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($namespace)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit'),
        		Theme :: get_common_image_path() . 'action_edit.png',
        		$this->browser->get_update_metadata_namespace_url($namespace),
        		ToolbarItem :: DISPLAY_ICON
        ));
        $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete'),
        		Theme :: get_common_image_path() . 'action_delete.png',
        		$this->browser->get_delete_metadata_namespace_url($namespace),
        		ToolbarItem :: DISPLAY_ICON
        ));
        
        return $toolbar->as_html();
    }
}
?>
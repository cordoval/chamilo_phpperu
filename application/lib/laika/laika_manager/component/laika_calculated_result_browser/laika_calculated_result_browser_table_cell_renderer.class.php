<?php
/**
 * $Id: laika_calculated_result_browser_table_cell_renderer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_calculated_result_browser
 */
require_once dirname(__FILE__) . '/laika_calculated_result_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/laika_calculated_result_table/default_laika_calculated_result_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../laika_calculated_result.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LaikaCalculatedResultBrowserTableCellRenderer extends DefaultLaikaCalculatedResultTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LaikaCalculatedResultBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $group)
    {
        if ($column === LaikaCalculatedResultBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($group);
        }
        
        return parent :: render_cell($column, $group);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($calculated_result)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_laika_calculated_result_attempt_viewing_url($calculated_result), 'label' => Translation :: get('Browse'), 'img' => Theme :: get_common_image_path() . 'action_browser.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
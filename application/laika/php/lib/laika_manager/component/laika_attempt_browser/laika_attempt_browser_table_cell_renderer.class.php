<?php
/**
 * $Id: laika_attempt_browser_table_cell_renderer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_attempt_browser
 */
require_once dirname(__FILE__) . '/laika_attempt_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/laika_attempt_table/default_laika_attempt_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../laika_attempt.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LaikaAttemptBrowserTableCellRenderer extends DefaultLaikaAttemptTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LaikaAttemptBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $group)
    {
        if ($column === LaikaAttemptBrowserTableColumnModel :: get_modification_column())
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
    private function get_modification_links($attempt)
    {
        $toolbar= new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Browse'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_laika_attempt_viewing_url($attempt), ToolbarItem :: DISPLAY_ICON ));
        
        return $toolbar->as_html();
    }
}
?>
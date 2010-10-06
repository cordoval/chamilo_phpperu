<?php
/**
 * $Id: laika_user_browser_table_cell_renderer.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.laika_user_browser
 */
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_manager/component/laika_user_browser_table_column_model.class.php';
require_once CoreApplication :: get_application_class_lib_path('user') . 'user_table/default_user_table_cell_renderer.class.php';
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_attempt.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class LaikaUserBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    /**
     * The repository browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function LaikaUserBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === LaikaUserBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user)
    {
        $toolbar= new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Browse'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_laika_user_viewing_url($user), ToolbarItem :: DISPLAY_ICON ));

        return $toolbar->as_html();
    }
}
?>
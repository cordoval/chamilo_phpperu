<?php
/**
 * $Id: user_approval_browser_table_cell_renderer.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.user_manager.component.user_approval_browser
 */
require_once dirname(__FILE__) . '/user_approval_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../user_table/default_user_table_cell_renderer.class.php';
/**
 * Cell renderer for the user object browser table
 */
class UserApprovalBrowserTableCellRenderer extends DefaultUserTableCellRenderer
{
    /**
     * The user browser component
     */
    private $browser;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function UserApprovalBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $user)
    {
        if ($column === UserApprovalBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($user);
        }
        
        return parent :: render_cell($column, $user);
    }

    /**
     * Gets the action links to display
     * @param $user The user for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($user)
    {
        $toolbar_data = array();
        
        $toolbar_data[] = array('href' => $this->browser->get_approve_user_url($user), 'label' => Translation :: get('Approve'), 'img' => Theme :: get_common_image_path() . 'action_activate.png');
        $toolbar_data[] = array('href' => $this->browser->get_deny_user_url($user), 'label' => Translation :: get('Deny'), 'img' => Theme :: get_common_image_path() . 'action_deinstall.png');
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
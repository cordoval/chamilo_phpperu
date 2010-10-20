<?php
/**
 * @package cda.tables.cas_account_table
 */
require_once dirname(__FILE__) . '/cas_account_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/cas_account_table/default_cas_account_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../cas_account.class.php';
require_once dirname(__FILE__) . '/../../cas_account_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class CasAccountBrowserTableCellRenderer extends DefaultCasAccountTableCellRenderer
{
    /**
     * The browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function CasAccountBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $cas_account)
    {
        if ($column === CasAccountBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($cas_account);
        }

        switch ($column->get_name())
        {
            case CasAccount :: PROPERTY_STATUS :
                return $cas_account->get_status_icon();
                break;
        }

        return parent :: render_cell($column, $cas_account);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($cas_account)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_cas_account_url($cas_account), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_cas_account_url($cas_account), ToolbarItem :: DISPLAY_ICON, true));

        if ($this->browser->get_user()->is_platform_admin())
        {
            if ($cas_account->is_enabled())
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Deactivate'), Theme :: get_image_path() . 'action_deactivate.png', $this->browser->get_deactivate_cas_account_url($cas_account), ToolbarItem :: DISPLAY_ICON));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Activate'), Theme :: get_image_path() . 'action_activate.png', $this->browser->get_activate_cas_account_url($cas_account), ToolbarItem :: DISPLAY_ICON));
            }
        }

        return $toolbar->as_html();
    }
}
?>
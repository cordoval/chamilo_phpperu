<?php
/**
 * @package cda.tables.cas_user_request_table
 */
require_once dirname(__FILE__) . '/cas_user_request_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/cas_user_request_table/default_cas_user_request_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../cas_user_request.class.php';
require_once dirname(__FILE__) . '/../../cas_user_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class CasUserRequestBrowserTableCellRenderer extends DefaultCasUserRequestTableCellRenderer
{
    /**
     * The browser component
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function CasUserRequestBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $cas_user_request)
    {
        if ($column === CasUserRequestBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($cas_user_request);
        }

        switch ($column->get_name())
        {
            case CasUserRequest :: PROPERTY_REQUESTER_ID :
                return $cas_user_request->get_requester_user()->get_fullname();
                break;
            case CasUserRequest :: PROPERTY_STATUS :
                return $cas_user_request->get_status_icon();
                break;
        }

        return parent :: render_cell($column, $cas_user_request);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($cas_user_request)
    {
        $toolbar = new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->browser->get_update_cas_user_request_url($cas_user_request), ToolbarItem :: DISPLAY_ICON));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->browser->get_delete_cas_user_request_url($cas_user_request), ToolbarItem :: DISPLAY_ICON, true));

        if ($this->browser->get_user()->is_platform_admin() && ($cas_user_request->is_pending() || $cas_user_request->is_rejected()))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Accept'), Theme :: get_image_path() . 'action_accept.png', $this->browser->get_accept_cas_user_request_url($cas_user_request), ToolbarItem :: DISPLAY_ICON));
            if ($cas_user_request->is_pending())
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Reject'), Theme :: get_image_path() . 'action_reject.png', $this->browser->get_reject_cas_user_request_url($cas_user_request), ToolbarItem :: DISPLAY_ICON));
            }
        }

        return $toolbar->as_html();
    }
}
?>
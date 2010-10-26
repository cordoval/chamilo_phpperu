<?php

namespace application\reservations;

use common\libraries\WebApplication;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use common\libraries\Translation;
use common\libraries\Theme;
use common\libraries\Utilities;
/**
 * $Id: item_browser_table_cell_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.item_browser
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/item_browser/item_browser_table_column_model.class.php';
require_once WebApplication :: get_application_class_lib_path('reservations') . 'tables/item_table/default_item_table_cell_renderer.class.php';

/**
 * Cell rendere for the learning object browser table
 */
class ItemBrowserTableCellRenderer extends DefaultItemTableCellRenderer
{
    /**
     * The repository browser component
     */
    protected $browser;

    /**
     * Constructor
     * @param ReservationsManager $browser
     */
    function ItemBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $item)
    {
        if ($column === ItemBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($item);
        }
        
        return parent :: render_cell($column, $item);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($item)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
    	if (Utilities :: get_classname_from_namespace(get_class($this->browser)) == 'ReservationsManagerAdminItemBrowserComponent')
        {
            if ($this->browser->get_user_id() == $item->get_creator() || $this->browser->has_right(ReservationsRights :: TYPE_ITEM, $item->get_id(), ReservationsRights :: DELETE_RIGHT) || $item->get_responsible() == $this->browser->get_user_id())
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Delete'),
		        		Theme :: get_common_image_path() . 'action_delete.png',
		        		$this->browser->get_delete_item_url($item->get_id(), $this->browser->get_category()),
		        		ToolbarItem :: DISPLAY_ICON,
		        		true
		        ));
            }
            
            if ($this->browser->get_user_id() == $item->get_creator() || $this->browser->has_right(ReservationsRights :: TYPE_ITEM, $item->get_id(), ReservationsRights :: EDIT_RIGHT) || $item->get_responsible() == $this->browser->get_user_id())
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Edit'),
		        		Theme :: get_common_image_path() . 'action_edit.png',
		        		$this->browser->get_update_item_url($item->get_id(), $this->browser->get_category()),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
                
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('ModifyRights'),
		        		Theme :: get_common_image_path() . 'action_rights.png',
		        		$this->browser->get_modify_rights_url(ReservationsRights :: TYPE_ITEM, $item->get_id()),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
        }
        
        if (Utilities :: get_classname_from_namespace(get_class($this->browser)) == 'ReservationsManagerAdminItemBrowserComponent')
        {
            $url = $this->browser->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $item->get_id()));
        }
        else
        {
            $url = $this->browser->get_browse_reservations_url($item->get_id());
        }
        
        if ($this->browser->get_user_id() == $item->get_creator() || $this->browser->has_right(ReservationsRights :: TYPE_ITEM, $item->get_id(), ReservationsRights :: VIEW_RIGHT))
        {
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('BrowseReservations'),
	        		Theme :: get_common_image_path() . 'action_browser.png',
	        		$url,
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }

        return $toolbar->as_html();
    }
}
?>
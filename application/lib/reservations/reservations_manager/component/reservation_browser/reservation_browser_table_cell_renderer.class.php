<?php
/**
 * $Id: reservation_browser_table_cell_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component.reservation_browser
 */
require_once dirname(__FILE__) . '/reservation_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/reservation_table/default_reservation_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../reservation.class.php';
require_once dirname(__FILE__) . '/../../../subscription.class.php';
require_once dirname(__FILE__) . '/../../reservations_manager.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class ReservationBrowserTableCellRenderer extends DefaultReservationTableCellRenderer
{
    /**
     * The repository browser component
     */
    protected $browser;
    private $reservation;

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function ReservationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $reservation)
    {
        if ($this->reservation != $reservation)
        {
            $conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation->get_id());
            $conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
            $condition = new AndCondition($conditions);
            
            $reservation->set_subscriptions($this->browser->count_subscriptions($condition));
            $this->reservation = $reservation;
        }
        
        if ($column === ReservationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($reservation);
        }
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case Reservation :: PROPERTY_MAX_USERS :
                    if ($reservation->get_type() == Reservation :: TYPE_TIMEPICKER)
                        return null;
                    return $reservation->get_subscriptions() . ' / ' . $reservation->get_max_users();
            }
        }
        
        return parent :: render_cell($column, $reservation);
    }

    /**
     * Gets the action links to display
     * @param ContentObject $content_object The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($reservation)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);
        
        if (get_class($this->browser) == 'ReservationsManagerAdminReservationBrowserComponent' && (
        	$this->browser->is_allowed_to_edit() ||
        	$this->browser->has_right('item', $reservation->get_item(), ReservationsRights :: EDIT_RIGHT)))
        {
            if ($reservation->get_subscriptions() == 0)
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Edit'),
		        		Theme :: get_common_image_path() . 'action_edit.png',
		        		$this->browser->get_update_reservation_url($reservation->get_id(), $this->browser->get_item()),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('EditNA'),
		        		Theme :: get_common_image_path() . 'action_edit_na.png',
		        		null,
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Delete'),
	        		Theme :: get_common_image_path() . 'action_delete.png',
	        		$this->browser->get_delete_reservation_url($reservation->get_id(), $this->browser->get_item()),
	        		ToolbarItem :: DISPLAY_ICON,
	        		true
	        ));
            
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('BrowseSubscriptions'),
	        		Theme :: get_common_image_path() . 'action_browser.png',
	        		$this->browser->get_admin_browse_subscription_url($reservation->get_id()),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        
        return $toolbar->as_html();
    }
}
?>
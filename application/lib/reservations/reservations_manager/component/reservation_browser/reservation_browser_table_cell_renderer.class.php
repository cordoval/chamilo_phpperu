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
        $toolbar_data = array();
        
        if (get_class($this->browser) == 'ReservationsManagerAdminReservationBrowserComponent' && $this->browser->has_right('item', $reservation->get_item(), ReservationsRights :: EDIT_RIGHT))
        {
            if ($reservation->get_subscriptions() == 0)
            {
                $toolbar_data[] = array('href' => $this->browser->get_update_reservation_url($reservation->get_id(), $this->browser->get_item()), 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
            }
            else
            {
                $toolbar_data[] = array('label' => Translation :: get('EditNA'), 'img' => Theme :: get_common_image_path() . 'action_edit_na.png');
            }
            
            $toolbar_data[] = array('href' => $this->browser->get_delete_reservation_url($reservation->get_id(), $this->browser->get_item()), 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);
            
            $toolbar_data[] = array('href' => $this->browser->get_admin_browse_subscription_url($reservation->get_id()), 'label' => Translation :: get('BrowseSubscriptions'), 'img' => Theme :: get_common_image_path() . 'action_browser.png');
        }
        
        return Utilities :: build_toolbar($toolbar_data);
    }
}
?>
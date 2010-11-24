<?php

namespace application\reservations;

use common\libraries\ObjectTableCellRenderer;
use common\libraries\EqualityCondition;
use common\libraries\DatetimeUtilities;
use common\libraries\Translation;
use common\libraries\Utilities;
/**
 * $Id: default_subscription_table_cell_renderer.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.subscription_table
 */
/**
 * TODO: Add comment
 */
class DefaultSubscriptionTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct($browser)
    {
    
    }
    
    protected $reservation;

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $subscription)
    {
        if (! $this->reservation || $this->reservation->get_id() != $subscription->get_reservation_id())
        {
            $this->reservation = $this->browser->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $subscription->get_reservation_id()))->next_result();
        }
        
        if ($property = $column->get_name())
        {
            switch ($property)
            {
                case Subscription :: PROPERTY_ID :
                    return $subscription->get_id();
                case Subscription :: PROPERTY_USER_ID :
                    $user = UserDataManager :: get_instance()->retrieve_user($subscription->get_user_id());
                    return $user->get_fullname();
                case Subscription :: PROPERTY_RESERVATION_ID :
                    {
                        $item = $this->browser->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $this->reservation->get_item()))->next_result();
                        return $item->get_name();
                    }
                case Subscription :: PROPERTY_START_TIME :
                    {
                        $time = $subscription->get_start_time();
                        if (!$time)
                        {
                            $time = $this->reservation->get_start_date();
                        }
                        return DatetimeUtilities :: format_locale_date(null, $time);
                    }
                case Subscription :: PROPERTY_STOP_TIME :
                    {
                        $time = $subscription->get_stop_time();
                        if (!$time)
                        {
                            $time = $this->reservation->get_stop_date();
                        }
                        return DatetimeUtilities :: format_locale_date(null, $time);
                    }
                case Subscription :: PROPERTY_ACCEPTED :
                    if ($subscription->get_accepted())
                        return Translation :: get('ConfirmYes', null, Utilities :: COMMON_LIBRARIES);
                    
                    return Translation :: get('No', null, Utilities :: COMMON_LIBRARIES);
            }
        
        }
        
        return '&nbsp;';
    }

    function render_id_cell($subscription)
    {
        return $subscription->get_id();
    }
}
?>
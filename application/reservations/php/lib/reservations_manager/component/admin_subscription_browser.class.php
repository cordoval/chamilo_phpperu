<?php

namespace application\reservations;

use common\libraries\BreadcrumbTrail;
use common\libraries\Breadcrumb;
use common\libraries\Translation;
use common\libraries\WebApplication;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\DatetimeUtilities;
use common\libraries\Theme;
/**
 * $Id: admin_subscription_browser.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once WebApplication :: get_application_class_lib_path('reservations') . 'reservations_manager/component/subscription_browser/subscription_browser_table.class.php';

class ReservationsManagerAdminSubscriptionBrowserComponent extends ReservationsManager
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        
        $reservation = $this->retrieve_reservations(new EqualityCondition(Reservation :: PROPERTY_ID, $this->get_reservation_id()))->next_result();
        $item = $this->retrieve_items(new EqualityCondition(Item :: PROPERTY_ID, $reservation->get_item()))->next_result();
        
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)), Translation :: get('ManageItems')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $reservation->get_item())), Translation :: get('ManageReservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_RESERVATION_ID => $this->get_reservation_id())), Translation :: get('ManageSubscriptions')));
        
        $this->display_header($trail);
        
        $this->display_reservation_information($item, $reservation);
        
        echo $this->get_user_html();
        $this->display_footer();
    }

    function get_user_html()
    {
        $table = new SubscriptionBrowserTable($this, $this->get_parameters(), $this->get_condition());
        
        $html = array();
        $html[] = $table->as_html();
        
        return implode($html, "\n");
    }

    function get_condition()
    {
        $reservation_id = $this->get_reservation_id();
        
        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, $reservation_id);
        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
        $condition = new AndCondition($conditions);
        
        return $condition;
    }

    function get_reservation_id()
    {
        $reservation_id = $_GET[ReservationsManager :: PARAM_RESERVATION_ID];
        return $reservation_id ? $reservation_id : 0;
    }

    function display_reservation_information($item, $reservation)
    {
        //$responsible = UserDataManager :: get_instance()->retrieve_user($item->get_responsible())->get_fullname();
        $responsible = $item->get_responsible();
        
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'treemenu_types/calendar_event.png);">';
        $html[] = '<div class="title">';
        $html[] = $item->get_name();
        $html[] = '</div>';
        $html[] = '<div class="description">';
        $html[] = $item->get_description();
        $html[] = '<b>' . Translation :: get('Responsible') . '</b>: ' . $responsible;
        $html[] = '<br /><b>' . Translation :: get('Type') . '</b>: ' . $this->get_reservation_type($reservation);
        $html[] = '<br /><b>' . Translation :: get('Start') . '</b>: ' . DatetimeUtilities :: format_locale_date(null, $reservation->get_start_date());
        $html[] = '<br /><b>' . Translation :: get('End') . '</b>: ' . DatetimeUtilities :: format_locale_date(null, $reservation->get_stop_date());
        $html[] = '</div>';
        $html[] = '</div>';
        echo implode("\n", $html);
    }

    function get_reservation_type($reservation)
    {
        switch ($reservation->get_type())
        {
            case Reservation :: TYPE_TIMEPICKER :
                return Translation :: get('Timepicker');
            case Reservation :: TYPE_BLOCK :
                return Translation :: get('Block');
        }
    }
}
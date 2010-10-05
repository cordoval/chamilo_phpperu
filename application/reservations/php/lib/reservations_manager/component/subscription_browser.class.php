<?php
/**
 * $Id: subscription_browser.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/subscription_browser/subscription_browser_table.class.php';
require_once dirname(__FILE__) . '/subscription_browser/user_quota_cellrenderer.class.php';
require_once dirname(__FILE__) . '/../../reservations_menu.class.php';

class ReservationsManagerSubscriptionBrowserComponent extends ReservationsManager
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('MySubscriptions')));

        $this->display_header($trail);
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
        $user_id = $this->get_user_id();

        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
        
        $time_conditions = array();
        $time_conditions[] = new AndCondition(new EqualityCondition(Reservation :: PROPERTY_TYPE, Reservation :: TYPE_BLOCK, Reservation :: get_table_name()), new InEqualityCondition(Reservation :: PROPERTY_STOP_DATE, InequalityCondition :: GREATER_THAN, time(), Reservation :: get_table_name()));
        $time_conditions[] = new AndCondition(new EqualityCondition(Reservation :: PROPERTY_TYPE, Reservation :: TYPE_TIMEPICKER, Reservation :: get_table_name()), new InEqualityCondition(Subscription :: PROPERTY_STOP_TIME, InequalityCondition :: GREATER_THAN, time()));

        $conditions[] = new OrCondition($time_conditions);
        
        $condition = new AndCondition($conditions);

        return $condition;
    }
}
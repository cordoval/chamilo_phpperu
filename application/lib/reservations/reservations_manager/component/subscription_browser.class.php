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
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('MySubscriptions')));

        $this->display_header($trail);
        echo $this->get_user_html();
        /*
		$rdm = ReservationsDataManager :: get_instance();
		$used_quota = ReservationsDataManager :: calculate_used_quota('1', $this->get_user_id());

		$table = new SimpleTable($used_quota, new UserQuotaCellRenderer(), null, 'user_quota');
		echo '<br /><h3>' . Translation :: get('UsedCredits') . '</h3>' . $table->toHTML();*/

        $this->display_footer();
    }

    function get_user_html()
    {
        $table = new SubscriptionBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_SUBSCRIPTIONS), $this->get_condition());

        $html = array();
        $html[] = $table->as_html();

        return implode($html, "\n");
    }

    function get_condition()
    {
        $user_id = $this->get_user_id();

        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_USER_ID, $user_id);
        $conditions[] = new EqualityCondition(Subscription :: PROPERTY_STATUS, Subscription :: STATUS_NORMAL);
        $condition = new AndCondition($conditions);

        return $condition;
    }
}
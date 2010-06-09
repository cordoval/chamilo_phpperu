<?php
/**
 * $Id: admin_reservation_browser.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.reservations_manager.component
 */
require_once dirname(__FILE__) . '/../reservations_manager.class.php';

require_once dirname(__FILE__) . '/reservation_browser/reservation_browser_table.class.php';
require_once dirname(__FILE__) . '/../../reservations_menu.class.php';
require_once dirname(__FILE__) . '/../../calendar/reservations_calendar_mini_month_renderer.class.php';
require_once dirname(__FILE__) . '/../../calendar/reservations_calendar_week_renderer.class.php';

class ReservationsManagerAdminReservationBrowserComponent extends ReservationsManager
{
    private $ab;

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => null)), Translation :: get('Reservations')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_ADMIN_BROWSE_ITEMS)), Translation :: get('ManageItems')));
        $trail->add(new Breadcrumb($this->get_url(array(ReservationsManager :: PARAM_ITEM_ID => $this->get_item())), Translation :: get('ManageReservations')));

        $this->ab = $this->get_action_bar();
        $this->display_header($trail);

        $time = isset($_GET['time']) ? intval($_GET['time']) : time();
        $minimonthcalendar = new ReservationsCalendarMiniMonthRenderer($this, $time);
        $weekrenderer = new ReservationsCalendarWeekRenderer($this, $time);

        echo $this->ab->as_html() . '<br />';
        echo '<div style="width: 20%; float: left;">' . $minimonthcalendar->render() . '</div>';
        echo '<div style="width: 75%; float: right;">' . $weekrenderer->render() . '</div><div class="clear">&nbsp</div><br /><br />';
        echo $this->get_user_html();
        $this->display_footer();
    }

    function get_user_html()
    {
        $table = new ReservationBrowserTable($this, array(ReservationsManager :: PARAM_ACTION => ReservationsManager :: ACTION_BROWSE_RESERVATIONS, ReservationsManager :: PARAM_ITEM_ID => $this->get_item()), $this->get_condition());

        $html = array();
        $html[] = '<div style="float: right; width: 100%;">';
        $html[] = $table->as_html();
        $html[] = '</div>';

        return implode($html, "\n");
    }

    function get_condition()
    {
        $item = $this->get_item();
        $conditions[] = new EqualityCondition(Reservation :: PROPERTY_ITEM, $item);
        $conditions[] = new EqualityCondition(Reservation :: PROPERTY_STATUS, Reservation :: STATUS_NORMAL);
        $condition = new AndCondition($conditions);

        /*$search = $this->ab->get_query();
		if(isset($search) && ($search != ''))
		{
			$conditions = array();
			$conditions[] = new PatternMatchCondition(Reservation :: PROPERTY_NOTES, '*' . $search . '*');
			$orcondition = new OrCondition($conditions);

			$conditions = array();
			$conditions[] = $orcondition;
			$conditions[] = $condition;
			$condition = new AndCondition($conditions);
		}*/
        return $condition;
    }

    function get_item()
    {
        return (isset($_GET[ReservationsManager :: PARAM_ITEM_ID]) ? $_GET[ReservationsManager :: PARAM_ITEM_ID] : 0);
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('Add'), Theme :: get_common_image_path() . 'action_add.png', $this->get_create_reservation_url($this->get_item()), ToolbarItem :: DISPLAY_ICON_AND_LABEL));

        return $action_bar;
    }
}
?>
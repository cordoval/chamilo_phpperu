<?php
/**
 * $Id: reservations_calendar_day_renderer.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.calendar
 */
require_once (dirname(__FILE__) . '/reservations_calendar_renderer.class.php');
/**
 * This personal calendar renderer provides a tabular week view of the events in
 * the calendar.
 */
class ReservationsCalendarListRenderer extends ReservationsCalendarRenderer
{

    /**
     * @see PersonalCalendarRenderer::render()
     */
    public function render($ids = null, $search_query = null)
    {
        if (! is_array($ids) || count($ids) == 0)
            return;

        if (count($ids) == 0)
        {
            $condition = new EqualityCondition(Subscription :: PROPERTY_RESERVATION_ID, - 1);
        }
        else
        {
            $conditions = array();
            $conditions[] = new InCondition(Reservation :: PROPERTY_ITEM, $ids, Reservation :: get_table_name());

            $conditions_time = array();
            $start = $this->get_start_time();
            $end = $this->get_end_time();
            $conditions_time[] = new AndCondition(new EqualityCondition(Reservation :: PROPERTY_TYPE, Reservation :: TYPE_BLOCK, Reservation :: get_table_name()), new InEqualityCondition(Reservation :: PROPERTY_START_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, $start, Reservation :: get_table_name()), new InEqualityCondition(Reservation :: PROPERTY_START_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, $end, Reservation :: get_table_name()));

            $conditions_time[] = new AndCondition(new EqualityCondition(Reservation :: PROPERTY_TYPE, Reservation :: TYPE_TIMEPICKER, Reservation :: get_table_name()), new InEqualityCondition(Subscription :: PROPERTY_START_TIME, InequalityCondition :: GREATER_THAN_OR_EQUAL, $start), new InEqualityCondition(Subscription :: PROPERTY_START_TIME, InequalityCondition :: LESS_THAN_OR_EQUAL, $end));

            $conditions[] = new OrCondition($conditions_time);

            if ($search_query && $search_query != '')
            {
                $query_conditions = array();

                $query_conditions[] = new PatternMatchCondition(Item :: PROPERTY_NAME, '*' . $search_query . '*', Item :: get_table_name());
                $query_conditions[] = new PatternMatchCondition(User :: PROPERTY_FIRSTNAME, '*' . $search_query . '*', User :: get_table_name());
                $query_conditions[] = new PatternMatchCondition(User :: PROPERTY_LASTNAME, '*' . $search_query . '*', User :: get_table_name());

                $conditions[] = new OrCondition($query_conditions);
            }

            $condition = new AndCondition($conditions);
        }

        $table = new SubscriptionOverviewBrowserTable($this->get_parent(), $this->get_parent()->get_parameters(), $condition);
        $parameters['time'] = '-TIME-';
        return $this->get_calendar_navigation($this->get_parent()->get_url($parameters)) . $table->as_html();
    }
    
    private function get_calendar_navigation($url_format)
    {
        $prev = strtotime('-1 Day', $this->get_time());
        $next = strtotime('+1 Day', $this->get_time());
        $navigation = new HTML_Table('class="calendar_navigation"');
        $navigation->updateCellAttributes(0, 0, 'style="text-align: left;"');
        $navigation->updateCellAttributes(0, 1, 'style="text-align: center;"');
        $navigation->updateCellAttributes(0, 2, 'style="text-align: right;"');
        $navigation->setCellContents(0, 0, '<a href="' . str_replace('-TIME-', $prev, $url_format) . '"><img src="' . Theme :: get_common_image_path() . 'action_prev.png" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
        $navigation->setCellContents(0, 1, date('l d F Y', $this->get_time()));
        $navigation->setCellContents(0, 2, ' <a href="' . str_replace('-TIME-', $next, $url_format) . '"><img src="' . Theme :: get_common_image_path() . 'action_next.png" style="vertical-align: middle;" alt="&gt;&gt;"/></a> ');
        return $navigation->toHtml();
    }
    
    private function get_start_time()
    {
    	return strtotime(date('Y-m-d 00:00:00', $this->get_time()));
    }
    
    private function get_end_time()
    {
    	return strtotime(date('Y-m-d 23:59:59', $this->get_time()));
    }
    
}
?>
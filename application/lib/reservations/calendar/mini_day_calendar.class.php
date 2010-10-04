<?php
/**
 * $Id: mini_day_calendar.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.calendar
 */
require_once (Path :: get_common_extensions_path() . 'calendar/calendar_table.class.php');
/**
 * A tabular representation of a week calendar
 */
class MiniDayCalendar extends CalendarTable
{
    /**
     * The navigation links
     */
    private $navigation_html;
    /**
     * The number of hours for one table cell.
     */
    private $hour_step;
    
    private $item_list;
    
    private $events;

    /**
     * Creates a new week calendar
     * @param int $display_time A time in the week to be displayed
     * @param int $hour_step The number of hours for one table cell. Defaults to
     * 2.
     */
    function MiniDayCalendar($display_time, $hour_step = 2, $item_list = array())
    {
        $this->navigation_html = '';
        $this->hour_step = $hour_step;
        $this->item_list = $item_list;
        parent :: CalendarTable($display_time);
        $this->build_table();
    }

    /**
     * Gets the number of hours for one table cell.
     * @return int
     */
    public function get_hour_step()
    {
        return $this->hour_step;
    }

    /**
     * Gets the first date which will be displayed by this calendar. This is
     * always a monday.
     * @return int
     */
    public function get_start_time()
    {
        return strtotime(date('Y-m-d 00:00:00', $this->get_display_time()));
    }

    /**
     * Gets the end date which will be displayed by this calendar. This is
     * always a sunday.
     * @return int
     */
    public function get_end_time()
    {
        return strtotime('-1 Second', strtotime('+1 Day', $this->get_start_time()));
    }

    /**
     * Builds the table
     */
    private function build_table()
    {
        $this->build_row_titles();
        
        $this->updateColAttributes(0, 'class="week_hours"');
        $this->updateColAttributes(0, 'style="height: 15px; width: 10px;"');
        for($hour = 0; $hour < 24; $hour += $this->hour_step)
        {
            $cell_content = $hour . ' - ' . ($hour + $this->hour_step);
            $this->setCellContents(0, $hour / $this->hour_step + 1, $cell_content);
            $this->updateColAttributes($hour / $this->hour_step + 1, 'style="width: 8%; height: 15px; padding-left: 0px; padding-right: 0px;"');
        }
        $this->setRowType(0, 'th');
        $this->setColType(0, 'th');
    }

    private function build_row_titles()
    {
        foreach ($this->item_list as $index => $item)
            $this->setCellContents($index + 1, 0, $item->get_name());
    }

    /**
     * Adds the events to the calendar
     */
    private function add_events()
    {
        $events = $this->events;
        foreach ($events as $time => $items)
        {
            $column = date('H', $time) / $this->hour_step + 1;
            foreach ($items as $item)
            {
                $content = $item['content'];
                $row = $item['index'] + 1;
                
                $cell_content = $this->getCellContents($row, $column);
                $cell_content .= $content;
                $this->setCellContents($row, $column, $cell_content);
            }
        }
    
    }

    function add_event($item_index, $table_date, $content)
    {
        $this->events[$table_date][] = array('index' => $item_index, 'content' => $content);
    }

    /**
     * Adds a navigation bar to the calendar
     * @param string $url_format The *TIME* in this string will be replaced by a
     * timestamp
     */
    public function add_calendar_navigation($url_format)
    {
        $day_number = date('z', $this->get_display_time());
        $prev = strtotime('-1 Day', $this->get_display_time());
        $next = strtotime('+1 Day', $this->get_display_time());
        $navigation = new HTML_Table('class="calendar_navigation"');
        $navigation->updateCellAttributes(0, 0, 'style="text-align: left;"');
        $navigation->updateCellAttributes(0, 1, 'style="text-align: center;"');
        $navigation->updateCellAttributes(0, 2, 'style="text-align: right;"');
        $navigation->setCellContents(0, 0, '<a href="' . str_replace('-TIME-', $prev, $url_format) . '"><img src="' . Theme :: get_theme_path() . 'action_prev.png" style="vertical-align: middle;" alt="&lt;&lt;"/></a> ');
        $navigation->setCellContents(0, 1, htmlentities(Translation :: get('Day')) . ' ' . $day_number . ' : ' . date('l d M Y', $this->get_start_time()));
        $navigation->setCellContents(0, 2, ' <a href="' . str_replace('-TIME-', $next, $url_format) . '"><img src="' . Theme :: get_theme_path() . 'action_next.png" style="vertical-align: middle;" alt="&gt;&gt;"/></a> ');
        $this->navigation_html = $navigation->toHtml();
    }

    /**
     * Returns a html-representation of this monthcalendar
     * @return string
     */
    public function toHtml()
    {
        $this->add_events();
        $html = parent :: toHtml();
        return $this->navigation_html . $html;
    }
}
?>
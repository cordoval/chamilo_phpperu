<?php
/**
 * $Id: default_period_table_cell_renderer.class.php 224 2009-11-13 14:40:30Z kariboe $
 * @package period.lib.period_table
 */
/**
 * TODO: Add comment
 */
class DefaultInternshipOrganizerPeriodTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerPeriodTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $period)
    {
        switch ($column->get_name())
        {
            case InternshipOrganizerPeriod :: PROPERTY_ID :
                return $period->get_id();
            case InternshipOrganizerPeriod :: PROPERTY_NAME :
                return $period->get_name();
            case InternshipOrganizerPeriod :: PROPERTY_DESCRIPTION :
                return $period->get_description();
            case InternshipOrganizerPeriod :: PROPERTY_BEGIN :
                return $this->get_date($period->get_begin());
            case InternshipOrganizerPeriod :: PROPERTY_END :
                return $this->get_date($period->get_end());
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
	private function get_date($date)
    {
            return date("d-m-Y", $date);
    }
}
?>
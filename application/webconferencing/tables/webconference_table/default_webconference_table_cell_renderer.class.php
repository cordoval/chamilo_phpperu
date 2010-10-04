<?php
/**
 * $Id: default_webconference_table_cell_renderer.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.tables.webconference_table
 */

require_once dirname(__FILE__) . '/../../webconference.class.php';

/**
 * Default cell renderer for the webconference table
 * @author Stefaan Vanbillemont
 */
class DefaultWebconferenceTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultWebconferenceTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Webconference $webconference - The webconference
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $webconference)
    {
        switch ($column->get_name())
        {
            case Webconference :: PROPERTY_ID :
                return $webconference->get_id();
            case Webconference :: PROPERTY_CONFNAME :
                return $webconference->get_confname();
            case Webconference :: PROPERTY_DESCRIPTION :
                return $webconference->get_description();
            case Webconference :: PROPERTY_DURATION :
                return $webconference->get_duration();
            default :
                return '&nbsp;';
        }
    }

    function render_id_cell($object)
    {
        return $object->get_id();
    }
}
?>
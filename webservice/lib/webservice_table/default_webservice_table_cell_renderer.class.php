<?php
/**
 * $Id: default_webservice_table_cell_renderer.class.php 208 2009-11-13 13:14:39Z vanpouckesven $
 * @package webservices.lib.webservice_table
 */

/**
 * TODO: Add comment
 */
class DefaultWebserviceTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultWebserviceTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $webservice)
    {
        switch ($column->get_name())
        {
            case WebserviceRegistration :: PROPERTY_NAME :
                return $webservice->get_name();
            case WebserviceRegistration :: PROPERTY_DESCRIPTION :
                $description = strip_tags($webservice->get_description());
                return Utilities :: truncate_string($description);
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
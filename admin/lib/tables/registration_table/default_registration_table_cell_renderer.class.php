<?php
/**
 * $Id: default_registration_table_cell_renderer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.tables.registration_table
 */
/**
 * TODO: Add comment
 */
class DefaultRegistrationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultRegistrationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $registration)
    {
        switch ($column->get_name())
        {
            case Registration :: PROPERTY_TYPE :
                return Translation :: get(Utilities :: underscores_to_camelcase($registration->get_type()));
            case Registration :: PROPERTY_NAME :
                return Utilities :: underscores_to_camelcase_with_spaces($registration->get_name());
            case Registration :: PROPERTY_VERSION :
                return $registration->get_version();
            case Registration :: PROPERTY_STATUS :
                $is_active = $registration->is_active();
                return '<span class="' . ($is_active ? 'active' : 'inactive') . '">' . Translation :: get($is_active ? 'Active' : 'Inactive') . '</span>';
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
<?php
/**
 * $Id: default_remote_package_table_cell_renderer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.tables.remote_package_table
 */

/**
 * TODO: Add comment
 */
class DefaultRemotePackageTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultRemotePackageTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $remote_package)
    {
        switch ($column->get_name())
        {
            case RemotePackage :: PROPERTY_SECTION :
            	return Translation :: get(Utilities::underscores_to_camelcase($remote_package->get_section()));
            case RemotePackage :: PROPERTY_NAME :
                return $remote_package->get_name();
            case RemotePackage :: PROPERTY_VERSION :
                return $remote_package->get_version();
            case RemotePackage :: PROPERTY_DESCRIPTION :
                return $remote_package->get_description();
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
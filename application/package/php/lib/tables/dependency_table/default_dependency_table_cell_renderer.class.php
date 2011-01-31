<?php

namespace application\package;

use common\libraries\ObjectTableCellRenderer;

/**
 * @package package.tables.package_language_table
 */
/**
 * Default cell renderer for the package_language table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultDependencyTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param PackageLanguage $package_language - The package_language
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $package)
    {
        switch ($column->get_name())
        {
            case Dependency :: PROPERTY_NAME :
                return $package->get_name();
//            case Dependency :: PROPERTY_SEVERITY :
//                return $package->get_severity_string();
//            case Dependency :: PROPERTY_COMPARE :
//                return $package->get_compare_string();
            case Dependency :: PROPERTY_VERSION :
                return $package->get_version();
            case Dependency :: PROPERTY_TYPE :
                return $package->get_type_name($package->get_type());
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
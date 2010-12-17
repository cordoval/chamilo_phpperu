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
class DefaultAuthorTableCellRenderer extends ObjectTableCellRenderer
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
            case Author :: PROPERTY_ID :
                return $package->get_id();
            case Author :: PROPERTY_NAME :
                return $package->get_name();
            case Author :: PROPERTY_EMAIL :
                return $package->get_email();
            case Author :: PROPERTY_COMPANY :
                return $package->get_company();
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
<?php

namespace application\package;

use common\libraries\ObjectTableCellRenderer;
use common\libraries\Translation;
/**
 * @package package.tables.package_language_table
 */
/**
 * Default cell renderer for the package_language table
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class DefaultPackageTableCellRenderer extends ObjectTableCellRenderer
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
            case Package :: PROPERTY_ID :
                return $package->get_id();
            case Package :: PROPERTY_SECTION :
                return $package->get_section();
            case Package :: PROPERTY_NAME :
                return $package->get_name();
            case Package :: PROPERTY_VERSION :
                return $package->get_version();
            case Package :: PROPERTY_DESCRIPTION :
                return $package->get_description();
            case Package :: PROPERTY_CYCLE_PHASE :
                return $package->get_cycle_phase();
            case Package :: PROPERTY_STATUS :
                return $package->get_status_string();
            case Package :: AUTHORS :
                
                $authors = $package->get_authors(false);
                if ($authors->size() > 0)
                {
                    $html = array();
                    $html[] = '<select>';
                    while ($author = $authors->next_result())
                    {
                        $html[] = '<option>' . $author->get_name() . '</option>';
                    }
                    $html[] = '</select>';
                    return implode("\n", $html);
                }
                else
                {
                    return Translation :: get('NoAuthors');
                }
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
<?php
namespace application\weblcms\tool\geolocation;

use application\weblcms\ObjectPublicationTableCellRenderer;

/**
 * $Id: geolocation_cell_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.geolocation.component.geolocation.browser
 */
/**
 * This class is a cell renderer for a publication candidate table
 */
class GeolocationCellRenderer extends ObjectPublicationTableCellRenderer
{

    function GeolocationCellRenderer($browser)
    {
        parent :: __construct($browser);
    }

    /*
	 * Inherited
	 */
    function render_cell($column, $publication)
    {
        if ($column === ObjectPublicationTableColumnModel :: get_action_column())
        {
            return $this->get_actions($publication, null, true, false)->as_html();
        }
        
        return parent :: render_cell($column, $publication);
    }

}
?>
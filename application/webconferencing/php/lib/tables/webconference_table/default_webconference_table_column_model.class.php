<?php

namespace application\webconferencing;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
/**
 * $Id: default_webconference_table_column_model.class.php 220 2009-11-13 14:33:52Z kariboe $
 * @package application.lib.webconferencing.tables.webconference_table
 */
require_once dirname(__FILE__) . '/../../webconference.class.php';

/**
 * Default column model for the webconference table
 * @author Stefaan Vanbillemont
 */
class DefaultWebconferenceTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function __construct()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return Array(ObjectTableColumn)
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(Webconference :: PROPERTY_CONFNAME);
        $columns[] = new ObjectTableColumn(Webconference :: PROPERTY_DESCRIPTION);
        $columns[] = new ObjectTableColumn(Webconference :: PROPERTY_DURATION);
        
        return $columns;
    }
}
?>
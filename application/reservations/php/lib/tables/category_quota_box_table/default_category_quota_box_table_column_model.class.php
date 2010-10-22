<?php

namespace application\reservations;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use common\libraries\Translation;
use common\libraries\Utilities;


/**
 * $Id: default_category_quota_box_table_column_model.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.category_quota_box_table
 */

/**
 * TODO: Add comment
 */
class DefaultCategoryQuotaBoxTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultCategoryQuotaBoxTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return ContentObjectTableColumn[]
     */
    private static function get_default_columns()
    {
        $columns = array();
        $columns[] = new ObjectTableColumn(QuotaBox :: PROPERTY_NAME, false);
        $columns[] = new ObjectTableColumn(QuotaBox :: PROPERTY_DESCRIPTION, false);
        return $columns;
    }
}
?>
<?php

namespace application\reservations;

use common\libraries\ObjectTableCellRenderer;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
/**
 * $Id: default_category_quota_box_table_cell_renderer.class.php 219 2009-11-13 14:28:13Z chellee $
 * @package application.reservations.tables.category_quota_box_table
 */

/**
 * TODO: Add comment
 */
class DefaultCategoryQuotaBoxTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultCategoryQuotaBoxTableCellRenderer($browser)
    {
    
    }
    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param Learning Object $content_object The learning object to render
     * @return string A HTML representation of the rendered table cell
     */
    
    private $qb;

    function render_cell($column, $quota_box_rel_category)
    {
        if ($title = $column->get_name())
        {
            $name = QuotaBox :: PROPERTY_NAME;
            $description = QuotaBox :: PROPERTY_DESCRIPTION;
            
            $qb = $this->qb;
            if (! $qb || $qb->get_id() != $quota_box_rel_category->get_quota_box_id())
            {
                $qb = $this->browser->retrieve_quota_boxes(new EqualityCondition(QuotaBox :: PROPERTY_ID, $quota_box_rel_category->get_quota_box_id()))->next_result();
                $this->qb = $qb;
            }
            
            switch ($title)
            {
                case $name :
                    return $qb->get_name();
                case $description :
                    return strip_tags($qb->get_description());
            }
        
        }
        
        return '&nbsp;';
    }

    function render_id_cell($quota_box_rel_category)
    {
        return $quota_box_rel_category->get_quota_box_id();
    }
}
?>
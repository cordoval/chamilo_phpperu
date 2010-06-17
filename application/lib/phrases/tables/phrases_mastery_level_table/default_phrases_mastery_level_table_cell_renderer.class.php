<?php
/**
 * $Id: default_phrases_mastery_level_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.tables.assessment_mastery_level_table
 */

require_once dirname(__FILE__) . '/../../phrases_mastery_level.class.php';

/**
 * Default cell renderer for the phrases_mastery_level table
 *
 * @author Hans De Bisschop
 * @author
 */
class DefaultPhrasesMasteryLevelTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultPhrasesMasteryLevelTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param PhrasesMasteryLevel $phrases_mastery_level - The phrases_mastery_level
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $phrases_mastery_level)
    {
        switch ($column->get_name())
        {
            case PhrasesMasteryLevel::PROPERTY_LEVEL :
                return Translation :: get($phrases_mastery_level->get_level());
            case PhrasesMasteryLevel::PROPERTY_UPGRADE_AMOUNT :
                return $phrases_mastery_level->get_upgrade_amount();
            case PhrasesMasteryLevel::PROPERTY_UPGRADE_SCORE :
                return $phrases_mastery_level->get_upgrade_score();
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
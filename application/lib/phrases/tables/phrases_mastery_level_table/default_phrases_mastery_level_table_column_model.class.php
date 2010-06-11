<?php
/**
 * $Id: default_phrases_mastery_level_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.tables.phrases_mastery_level_table
 */
require_once dirname(__FILE__) . '/../../phrases_mastery_level.class.php';

/**
 * Default column model for the phrases_mastery_level table
 *
 * @author Hans De Bisschop
 * @author
 */
class DefaultPhrasesMasteryLevelTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultPhrasesMasteryLevelTableColumnModel()
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
        $columns[] = new ObjectTableColumn(PhrasesMasteryLevel :: PROPERTY_LEVEL, false);
        $columns[] = new ObjectTableColumn(PhrasesMasteryLevel :: PROPERTY_UPGRADE_AMOUNT, false);
        $columns[] = new ObjectTableColumn(PhrasesMasteryLevel :: PROPERTY_UPGRADE_SCORE, false);

        return $columns;
    }
}
?>
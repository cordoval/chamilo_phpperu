<?php
/**
 * $Id: default_phrases_publication_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.tables.phrases_publication_table
 */
require_once dirname(__FILE__) . '/../../phrases_publication.class.php';

/**
 * Default column model for the phrases_publication table
 *
 * @author Hans De Bisschop
 * @author
 */
class DefaultPhrasesPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultPhrasesPublicationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return Array(ObjectTableColumn)
     */
    private static function get_default_columns()
    {
        $content_object_alias = RepositoryDataManager :: get_instance()->get_alias(ContentObject :: get_table_name());

        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, true, $content_object_alias);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, true, $content_object_alias);
        $columns[] = new ObjectTableColumn(PhrasesMasteryLevel :: PROPERTY_LEVEL, false);
        $columns[] = new ObjectTableColumn(Language :: PROPERTY_ORIGINAL_NAME, false);
        $columns[] = new StaticTableColumn(Translation :: get('NumberOfQuestions'));

        return $columns;
    }
}
?>
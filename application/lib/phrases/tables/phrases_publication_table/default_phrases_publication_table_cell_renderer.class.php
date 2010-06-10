<?php
/**
 * $Id: default_phrases_publication_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.tables.assessment_publication_table
 */

require_once dirname(__FILE__) . '/../../phrases_publication.class.php';

/**
 * Default cell renderer for the phrases_publication table
 *
 * @author Hans De Bisschop
 * @author
 */
class DefaultPhrasesPublicationTableCellRenderer implements ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function DefaultPhrasesPublicationTableCellRenderer()
    {
    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param PhrasesPublication $phrases_publication - The phrases_publication
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $phrases_publication)
    {
        $content_object = $phrases_publication->get_publication_object();

        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                return $content_object->get_title();
            case ContentObject :: PROPERTY_DESCRIPTION :
                $description = Utilities :: truncate_string($content_object->get_description(), 200);
                return $description;
            case PhrasesMasteryLevel :: PROPERTY_LEVEL :
                return Translation :: get($phrases_publication->get_mastery_level()->get_level());
            case Language :: PROPERTY_ORIGINAL_NAME :
                return AdminDataManager :: get_instance()->retrieve_language($phrases_publication->get_language_id())->get_original_name();
            case Translation :: get('NumberOfQuestions') :
                return $content_object->count_questions();
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
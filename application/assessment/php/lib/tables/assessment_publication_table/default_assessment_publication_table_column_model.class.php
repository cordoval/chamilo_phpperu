<?php
/**
 * $Id: default_assessment_publication_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.tables.assessment_publication_table
 */
require_once dirname(__FILE__) . '/../../assessment_publication.class.php';

/**
 * Default column model for the assessment_publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultAssessmentPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultAssessmentPublicationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 1);
    }

    /**
     * Gets the default columns for this model
     * @return Array(ObjectTableColumn)
     */
    private static function get_default_columns()
    {
        $rdm = RepositoryDataManager :: get_instance();
        $content_object_alias = $rdm->get_alias(ContentObject :: get_table_name());
        
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, true, $content_object_alias);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, true, $content_object_alias);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TYPE, true, $content_object_alias);
        //$columns[] = new ObjectTableColumn(AssessmentPublication :: PROPERTY_FROM_DATE);
        //$columns[] = new ObjectTableColumn(AssessmentPublication :: PROPERTY_TO_DATE);
        //		$columns[] = new ObjectTableColumn(AssessmentPublication :: PROPERTY_PUBLISHER);
        

        return $columns;
    }
}
?>
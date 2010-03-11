<?php
/**
 * $Id: default_survey_publication_table_column_model.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.tables.survey_publication_table
 */
require_once dirname(__FILE__) . '/../../survey_publication.class.php';

/**
 * Default column model for the survey_publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultSurveyPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultSurveyPublicationTableColumnModel()
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
        $content_object_alias = $rdm->get_database()->get_alias(ContentObject :: get_table_name());
        
        $columns = array();
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE, true, $content_object_alias);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION, true, $content_object_alias);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TYPE, true, $content_object_alias);
        //$columns[] = new ObjectTableColumn(SurveyPublication :: PROPERTY_FROM_DATE);
        //$columns[] = new ObjectTableColumn(SurveyPublication :: PROPERTY_TO_DATE);
        //		$columns[] = new ObjectTableColumn(SurveyPublication :: PROPERTY_PUBLISHER);
        

        return $columns;
    }
}
?>
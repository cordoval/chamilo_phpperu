<?php

require_once dirname(__FILE__) . '/../../publication.class.php';

/**
 * Default column model for the publication table
 *
 * @author Sven Vanpoucke
 * @author 
 */
class DefaultInternshipOrganizerPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultInternshipOrganizerPublicationTableColumnModel()
    {
        parent :: __construct(self :: get_default_columns(), 0);
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
       	$columns[] = new ObjectTableColumn(InternshipOrganizerPublication :: PROPERTY_FROM_DATE);
       	$columns[] = new ObjectTableColumn(InternshipOrganizerPublication :: PROPERTY_TO_DATE);
      
        return $columns;
    }
}
?>
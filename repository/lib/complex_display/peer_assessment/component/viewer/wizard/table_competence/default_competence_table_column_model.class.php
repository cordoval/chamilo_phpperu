<?php
//require_once dirname(__FILE__) . '/../../peer_assessment_publication.class.php';

/**
 * Default column model for the peer_assessment_publication table
 * @author Nick Van Loocke
 */
class DefaultCompetenceTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultCompetenceTableColumnModel($columns)
    {
        parent :: __construct(empty($columns) ? self :: get_default_columns() : $columns, 1);
    }

    /**
     * Gets the default columns for this model
     * @return Array(ObjectTableColumn)
     */
    private static function get_default_columns()
    {
        $columns = array();
    
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
        
        return $columns;
    }
}
?>
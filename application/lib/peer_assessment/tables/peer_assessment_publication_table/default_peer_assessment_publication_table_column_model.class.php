<?php
require_once dirname(__FILE__) . '/../../peer_assessment_publication.class.php';

/**
 * Default column model for the peer_assessment_publication table
 * @author Nick Van Loocke
 */
class DefaultPeerAssessmentPublicationTableColumnModel extends ObjectTableColumnModel
{

    /**
     * Constructor
     */
    function DefaultPeerAssessmentPublicationTableColumnModel($columns)
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
        
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_ID);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_TITLE);
        $columns[] = new ObjectTableColumn(ContentObject :: PROPERTY_DESCRIPTION);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_PARENT_ID);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_CATEGORY);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_FROM_DATE);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_TO_DATE);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_HIDDEN);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_PUBLISHER);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_PUBLISHED);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_MODIFIED);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_DISPLAY_ORDER);
        //		$columns[] = new ObjectTableColumn(PeerAssessmentPublication :: PROPERTY_EMAIL_SENT);
        

        return $columns;
    }
}
?>
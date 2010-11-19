<?php

namespace application\peer_assessment;

use common\libraries\ObjectTableColumnModel;
use common\libraries\ObjectTableColumn;
use repository\ContentObject;

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
    function __construct($columns)
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
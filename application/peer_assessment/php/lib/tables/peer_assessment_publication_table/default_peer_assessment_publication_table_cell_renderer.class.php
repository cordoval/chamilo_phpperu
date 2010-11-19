<?php

namespace application\peer_assessment;

use common\libraries\ObjectTableCellRenderer;
use repository\ContentObject;
use repository\content_object\peer_assessment\PeerAssessmentDisplay;

require_once dirname(__FILE__) . '/../../peer_assessment_publication.class.php';
//require_once Path :: get_repository_path() . 'lib/complex_display/peer_assessment/peer_assessment_display.class.php';

/**
 * Default cell renderer for the peer_assessment_publication table
 * @author Nick Van Loocke
 */
class DefaultPeerAssessmentPublicationTableCellRenderer extends ObjectTableCellRenderer
{

    /**
     * Constructor
     */
    function __construct()
    {

    }

    /**
     * Renders a table cell
     * @param ContentObjectTableColumnModel $column The column which should be
     * rendered
     * @param PeerAssessmentPublication $peer_assessment_publication - The peer_assessment_publication
     * @return string A HTML representation of the rendered table cell
     */
    function render_cell($column, $peer_assessment_publication)
    {
        switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
                $url = $this->browser->get_url(array(PeerAssessmentManager :: PARAM_ACTION => PeerAssessmentManager :: ACTION_VIEW_PEER_ASSESSMENT, PeerAssessmentDisplay :: PARAM_DISPLAY_ACTION => PeerAssessmentDisplay :: ACTION_VIEW_PEER_ASSESSMENT, PeerAssessmentManager :: PARAM_PEER_ASSESSMENT_PUBLICATION => $peer_assessment_publication->get_id()));
                return '<a href="' . $url . '">' . htmlspecialchars($peer_assessment_publication->get_content_object()->get_title()) . '</a>';
            case ContentObject :: PROPERTY_DESCRIPTION :
                return $peer_assessment_publication->get_content_object()->get_description();
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
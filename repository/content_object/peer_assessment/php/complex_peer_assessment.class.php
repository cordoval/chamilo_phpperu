<?php
namespace repository\content_object\peer_assessment;

use repository\content_object\competence\Competence;

use repository\ComplexContentObjectItem;

/**
 * $Id: complex_peer_assessment.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.peer_assessment
 * @author Sven Vanpoucke
 */

class ComplexPeerAssessment extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(Competence :: get_type_name());
    }
}
?>
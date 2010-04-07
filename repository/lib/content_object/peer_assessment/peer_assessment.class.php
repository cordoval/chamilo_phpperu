<?php
/**
 *  $Id: peer_assessment.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.peer_assessment
 *  @author Sven Vanpoucke
 */
/**
 * This class represents an peer_assessment
 */
class PeerAssessment extends ContentObject
{
	function get_allowed_types()
    {
        return array('competence');
    }
}
?>
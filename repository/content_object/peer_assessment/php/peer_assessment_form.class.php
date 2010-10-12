<?php
/**
 *  $Id: peer_assessment_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.peer_assessment
 *  @author Sven Vanpoucke
 */
require_once dirname(__FILE__) . '/peer_assessment.class.php';
/**
 * This class represents a form to create or update peer_assessments
 */
class PeerAssessmentForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new PeerAssessment();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
?>
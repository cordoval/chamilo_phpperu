<?php
/**
 *  $Id: competence_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 *  @package repository.lib.content_object.competence
 *  @author Sven Vanpoucke
 *
 */
require_once dirname(__FILE__) . '/competence.class.php';
/**
 * This class represents a form to create or update competences
 */
class CompetenceForm extends ContentObjectForm
{

    // Inherited
    function create_content_object()
    {
        $object = new Competence();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }
}
?>

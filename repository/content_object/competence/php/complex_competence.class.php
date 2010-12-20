<?php
namespace repository\content_object\competence;

use repository\content_object\indicator\Indicator;

use repository\ComplexContentObjectItem;
/**
 * $Id: complex_competence.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.competence
 * @author Sven Vanpoucke
 */

class ComplexCompetence extends ComplexContentObjectItem
{

    function get_allowed_types()
    {
        return array(Indicator :: get_type_name());
    }
}
?>
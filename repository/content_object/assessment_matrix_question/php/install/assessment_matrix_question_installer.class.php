<?php
namespace repository\content_object\assessment_matrix_question;

use repository\ContentObjectInstaller;

/**
 * $Id: assessment_matrix_question_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class AssessmentMatrixQuestionContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>
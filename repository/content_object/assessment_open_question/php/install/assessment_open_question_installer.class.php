<?php
namespace repository\content_object\assessment_open_question;

use repository\ContentObjectInstaller;

/**
 * $Id: assessment_open_question_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class AssessmentOpenQuestionContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>
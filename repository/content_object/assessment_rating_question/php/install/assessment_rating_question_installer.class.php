<?php
namespace repository\content_object\assessment_rating_question;

use repository\ContentObjectInstaller;

/**
 * $Id: assessment_rating_question_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class AssessmentRatingQuestionContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>
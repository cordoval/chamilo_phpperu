<?php
namespace repository\content_object\match_question;

use repository\ContentObjectInstaller;

/**
 * $Id: match_question_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class MatchQuestionContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>
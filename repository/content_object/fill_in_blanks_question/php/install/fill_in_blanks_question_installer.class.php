<?php
namespace repository\content_object\fill_in_blanks_question;

use repository\ContentObjectInstaller;

/**
 * $Id: fill_in_blanks_question_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
class FillInBlanksQuestionContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
}
?>
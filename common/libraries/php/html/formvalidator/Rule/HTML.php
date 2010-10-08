<?php
/**
 * @package common.html.formvalidator.Rule
 */
// $Id: HTML.php 128 2009-11-09 13:13:20Z vanpouckesven $
require_once ('HTML/QuickForm/Rule.php');
require_once (Path :: get_plugin_path() . 'kses-0.2.2/kses.php');
/**
 * QuickForm rule to check a html
 */
class HTML_QuickForm_Rule_HTML extends HTML_QuickForm_Rule
{

    /**
     * Function to validate HTML
     * @see HTML_QuickForm_Rule
     * @param string $html
     * @return boolean True if html is valid
     */
    function validate($html, $mode = NO_HTML)
    {
        $allowed_tags = $this->get_allowed_tags($mode, $fullpage);
        $cleaned_html = kses($html, $allowed_tags);
        return $html == $cleaned_html;
    }

    /**
     * Get allowed tags
     * @param int $mode NO_HTML, STUDENT_HTML, TEACHER_HTML,
     * STUDENT_HTML_FULLPAGE or TEACHER_HTML_FULLPAGE
     * @param boolean $fullpage If true, the allowed tags for full-page editing
     * are returned.
     */
    function get_allowed_tags($mode)
    {
        //Include the allowed tags
        include (dirname(__FILE__) . '/allowed_tags.inc.php');
        switch ($mode)
        {
            case NO_HTML :
                return array();
            case STUDENT_HTML :
                return $allowed_tags_student;
            case STUDENT_HTML_FULLPAGE :
                return array_merge($allowed_tags_student, $allowed_tags_student_full_page);
            case TEACHER_HTML :
                return $allowed_tags_teacher;
            case TEACHER_HTML_FULLPAGE :
                return array_merge($allowed_tags_teacher, $allowed_tags_teacher_full_page);
            default :
                return array();
        }
    }
}
?>
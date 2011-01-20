<?php
//FIXME do not use such a script because it requires unneeded files and
// creates cyclic dependencies
use common\libraries\Path;

require_once Path :: get_repository_content_object_path() . 'assessment_multiple_choice_question/php/assessment_multiple_choice_question_option.class.php';
require_once Path :: get_repository_content_object_path() . 'assessment_matching_question/php/assessment_matching_question_option.class.php';
require_once  Path::get_common_libraries_path() .'php/ims/main.php';
require_once dirname(__FILE__) . '/qti_import.class.php';

require_once_all(dirname(__FILE__) .'/*.class.php');
require_once_all(dirname(__FILE__) . '/object_import/*.class.php');

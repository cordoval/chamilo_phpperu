<?php
use common\libraries\Path;

require_once Path :: get_common_libraries_path() . 'php/ims/main.php';
require_once dirname(__FILE__) . '/../qti/main.php';
require_once Path :: get_application_path() . 'weblcms/php/lib/course/course.class.php';

require_once_all(dirname(__FILE__) . '/object_export/*.class.php');
require_once_all(dirname(__FILE__) . '/*.class.php');

?>
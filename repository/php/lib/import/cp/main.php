<?php
use common\libraries;
use common\libraries\Path;

require_once  Path::get_common_libraries_path() .'php/ims/main.php';

//require_once Path::get_application_path() . 'lib/weblcms/course/course.class.php';
//require_once Path::get_application_path() . 'lib/weblcms/category_manager/course_category.class.php';
require_once Path::get_repository_content_object_path() .'learning_path_item/php/complex_learning_path_item.class.php';
require_once Path::get_repository_content_object_path() .'portfolio_item/php/complex_portfolio_item.class.php';

require_once_all(dirname(__FILE__) .'/metadata/*.class.php');
require_once_all(dirname(__FILE__) .'/reader/*.class.php');
require_once_all(dirname(__FILE__) .'/util/*.class.php');
require_once_all(dirname(__FILE__) .'/object_import/*.class.php');
require_once_all(dirname(__FILE__) .'/object_import/import/*.class.php');
require_once_all(dirname(__FILE__) .'/organization_import/*.class.php');


//require_once 'cp_chamilo.class.php';



?>
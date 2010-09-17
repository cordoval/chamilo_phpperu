<?php

require_once  Path::get_common_path() .'ims/main.php';

require_once Path::get_application_path() . 'lib/weblcms/course/course.class.php';
require_once Path::get_application_path() . 'lib/weblcms/category_manager/course_category.class.php';
require_once Path::get_repository_path() .'lib/content_object/learning_path_item/complex_learning_path_item.class.php';
require_once Path::get_repository_path() .'lib/content_object/portfolio_item/complex_portfolio_item.class.php';

require_once_all(dirname(__FILE__) .'/metadata/*.class.php');
require_once_all(dirname(__FILE__) .'/reader/*.class.php');
require_once_all(dirname(__FILE__) .'/util/*.class.php');
require_once_all(dirname(__FILE__) .'/object_import/*.class.php');
require_once_all(dirname(__FILE__) .'/object_import/import/*.class.php');
require_once_all(dirname(__FILE__) .'/organization_import/*.class.php');


//require_once 'cp_chamilo.class.php';



?>
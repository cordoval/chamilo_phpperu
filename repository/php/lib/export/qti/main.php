<?php

//TODO remove these lines and replace by autoloading !
use common\libraries\Path;

require_once  Path::get_common_libraries_path() .'php/ims/main.php';

require_once_all(dirname(__FILE__) .'/*.class.php');

require_once dirname(__FILE__) . '/object_export/qti_serializer_base.class.php';

require_once_all(dirname(__FILE__) .'/object_export/*.class.php');
require_once_all(dirname(__FILE__) .'/object_export/serializer/*.class.php');

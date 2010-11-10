<?php
namespace common\libraries;

use repository\RepositoryDataManager;
use repository\ContentObject;

require_once dirname(__FILE__) . '/../../../../global.inc.php';

$content_objects = RepositoryDataManager :: get_registered_types();

foreach ($content_objects as $content_object)
{
    $css_file = Path :: get_repository_content_object_path() . $content_object . '/resources/css/aqua/aqua.css';

    if (file_exists($css_file))
    {
        $content = file_get_contents($css_file);
    }
    else
    {

        $content = '@CHARSET "ISO-8859-1";';
    }

    $content .= '
ul.tree-menu li div a.' . $content_object . ' {
	background-image: url(../../images/aqua/logo/16.png);
}

ul.tree-menu li div a.type_' . $content_object . ' {
	background-image: url(../../images/aqua/logo/16.png);
}';

//    if (! file_exists($css_file))
//    {
//        $resources = Path :: get_repository_content_object_path() . $content_object . '/resources/';
//        if (! file_exists($resources))
//        {
//            mkdir($resources);
//        }
//
//        $css = Path :: get_repository_content_object_path() . $content_object . '/resources/css/';
//        if (! file_exists($css))
//        {
//            mkdir($css);
//        }
//
//        $aqua = Path :: get_repository_content_object_path() . $content_object . '/resources/css/aqua/';
//        if (! file_exists($aqua))
//        {
//            mkdir($aqua);
//        }
//    }
//
//    if (! $handle = fopen($css_file, 'w+'))
//    {
//        exit();
//    }
//
//    // Write $somecontent to our opened file.
//    if (fwrite($handle, $content) === FALSE)
//    {
//        exit();
//    }
//
//    echo "Success, wrote to file ($css_file)<br />";
//
//    fclose($handle);
}
?>
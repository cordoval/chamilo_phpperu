<?php
namespace application\alexia;

use common\libraries\WebApplication;

require_once WebApplication :: get_application_class_lib_path('alexia') . 'rss/publication_rss.class.php';

$rss = new AlexiaPublicationRSS();
echo $rss->build_rss();
?>
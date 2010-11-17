<?php

namespace application\profiler;
require_once dirname(__FILE__).'/../../../../../../common/global.inc.php';
use common\libraries\WebApplication;

require_once WebApplication :: get_application_class_lib_path('profiler') . 'rss/publication_rss.class.php';

$rss = new ProfilerPublicationRSS();
echo $rss->build_rss();
?>
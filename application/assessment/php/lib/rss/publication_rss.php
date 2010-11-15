<?php

namespace application\assessment;

use common\libraries\Path;
require_once dirname(__FILE__).'/publication_rss.class.php';
require_once Path :: get_common_path() . 'global.inc.php';

$rss = new AssessmentPublicationRSS();
echo $rss->build_rss();
?>
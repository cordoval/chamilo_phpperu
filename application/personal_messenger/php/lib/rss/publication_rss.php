<?php
namespace application\personal_messenger;

use common\libraries\WebApplication;
require_once dirname(__FILE__).'/../../../../../common/global.inc.php';

require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'rss/publication_rss.class.php';

$rss = new PersonalMessengerPublicationRSS();
echo $rss->build_rss();
?>
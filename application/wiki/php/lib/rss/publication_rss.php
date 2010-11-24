<?php
namespace application\wiki;
require_once dirname(__FILE__).'/../../../../common/global.inc.php';

$rss = new WikiPublicationRSS();
echo $rss->build_rss();
?>
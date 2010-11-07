<?php
namespace application\wiki;

$rss = new WikiPublicationRSS();
echo $rss->build_rss();
?>
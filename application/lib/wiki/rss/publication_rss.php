<?php
require_once dirname(__FILE__).'/publication_rss.class.php';

$rss = new WikiPublicationRSS();
echo $rss->build_rss();
?>
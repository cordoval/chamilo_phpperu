<?php
require_once dirname(__FILE__) . '/../global.inc.php';
require_once dirname(__FILE__).'/all_publications_rss.class.php';

$pubrss = new AllPublicationsRSS();
echo $pubrss->build_rss();

?>
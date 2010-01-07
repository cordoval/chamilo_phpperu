<?php
require_once dirname(__FILE__) . '/../global.inc.php';
require_once dirname(__FILE__).'/publication_rss.class.php';

$pubrss = new PublicationRSS();
echo $pubrss->build_rss();

?>
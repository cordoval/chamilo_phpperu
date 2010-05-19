<?php
require_once dirname(__FILE__).'/publication_rss.class.php';

$rss = new PersonalCalendarPublicationRSS();
echo $rss->build_rss();
?>
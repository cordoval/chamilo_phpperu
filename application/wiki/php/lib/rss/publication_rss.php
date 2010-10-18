<?php
require_once WebApplication :: get_application_class_lib_path('wiki') . 'rss/publication_rss.class.php';

$rss = new WikiPublicationRSS();
echo $rss->build_rss();
?>
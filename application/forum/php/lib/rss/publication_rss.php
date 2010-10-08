<?php
require_once WebApplication :: get_application_class_lib_path('forum') . 'rss/publication_rss.class.php';

$rss = new ForumPublicationRSS();
echo $rss->build_rss();
?>
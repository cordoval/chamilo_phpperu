<?php
require_once WebApplication :: get_application_class_lib_path('personal_messenger') . 'rss/publication_rss.class.php';

$rss = new PersonalMessengerPublicationRSS();
echo $rss->build_rss();
?>
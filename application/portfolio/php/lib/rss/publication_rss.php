<?php
namespace application\portfolio;

require_once dirname(__FILE__).'/../../../../../common/global.inc.php';
require_once dirname(__FILE__).'/publication_rss.class.php';

$rss = new PortfolioPublicationRSS();
echo $rss->build_rss();
?>
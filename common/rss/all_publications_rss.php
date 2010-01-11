<?php
require_once dirname(__FILE__) . '/../global.inc.php';
require_once dirname(__FILE__).'/builders/basic_rss.class.php';
require_once dirname(__FILE__).'/builders/combined_rss.class.php';

require_once Path :: get_application_path().'/lib/alexia/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/assessment/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/forum/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/personal_calendar/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/personal_messenger/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/portfolio/rss/publication_rss.class.php';
require_once Path :: get_application_path().'/lib/weblcms/rss/publication_rss.class.php';


class AllPublicationsRSS extends CombinedRSS
{
	function AllPublicationsRSS()
	{
		parent :: CombinedRSS('Chamilo publications', 'http://localhost', 'Chamilo publications', 'http://localhost');
	}
	
	function get_basic_rss_objects()
	{
		$streams = array();
		$streams[] = new AlexiaPublicationRSS();
		$streams[] = new AssessmentPublicationRSS();
		$streams[] = new ForumPublicationRSS();
		$streams[] = new PersonalCalendarPublicationRSS();
		$streams[] = new PersonalMessengerPublicationRSS();
		$streams[] = new PortfolioPublicationRSS();
		$streams[] = new WeblcmsPublicationRSS();
		return $streams;
	}
	
}

$pubrss = new AllPublicationsRSS();
echo $pubrss->build_rss();
?>
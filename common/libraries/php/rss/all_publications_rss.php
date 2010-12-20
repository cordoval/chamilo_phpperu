<?php
namespace common\libraries;

use application\wiki\WikiPublicationRSS;
use application\weblcms\WeblcmsPublicationRSS;
use application\profiler\ProfilerPublicationRSS;
use application\portfolio\PortfolioPublicationRSS;
use application\personal_messenger\PersonalMessengerPublicationRSS;
use application\personal_calendar\PersonalCalendarPublicationRSS;
use application\forum\ForumPublicationRSS;
use application\assessment\AssessmentPublicationRSS;
use application\alexia\AlexiaPublicationRSS;

require_once dirname(__FILE__) . '/../global.inc.php';
require_once dirname(__FILE__) . '/builders/basic_rss.class.php';
require_once dirname(__FILE__) . '/builders/combined_rss.class.php';

require_once Path :: get_application_path() . '/lib/alexia/rss/publication_rss.class.php';
require_once Path :: get_application_path() . '/lib/assessment/rss/publication_rss.class.php';
require_once Path :: get_application_path() . '/lib/forum/rss/publication_rss.class.php';
require_once Path :: get_application_path() . '/lib/personal_calendar/rss/publication_rss.class.php';
require_once Path :: get_application_path() . '/lib/personal_messenger/rss/publication_rss.class.php';
require_once Path :: get_application_path() . '/lib/portfolio/rss/publication_rss.class.php';
require_once Path :: get_application_path() . '/lib/profiler/rss/publication_rss.class.php';
require_once Path :: get_application_path() . '/lib/weblcms/rss/publication_rss.class.php';
require_once Path :: get_application_path() . '/lib/wiki/rss/publication_rss.class.php';

class AllPublicationsRSS extends CombinedRSS
{

    function __construct()
    {
        parent :: __construct('Chamilo publications', htmlspecialchars(Path :: get(WEB_PATH)), 'Chamilo publications', htmlspecialchars(Path :: get(WEB_PATH) . 'common/rss/all_publications_rss.php'));
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
        $streams[] = new ProfilerPublicationRSS();
        $streams[] = new WeblcmsPublicationRSS();
        $streams[] = new WikiPublicationRSS();
        return $streams;
    }

}

$pubrss = new AllPublicationsRSS();
echo $pubrss->build_rss();
?>
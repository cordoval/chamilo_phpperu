<?php
namespace application\personal_calendar;

use common\libraries\Redirect;
use common\libraries\WebApplication;
use common\libraries\Application;
use common\libraries\Path;
use common\libraries\PublicationRSS;

use repository\RepositoryDataManager;

require_once Path :: get_common_libraries_class_path() . '/rss/publication_rss.class.php';
require_once WebApplication :: get_application_class_lib_path('personal_calendar') . 'data_manager/database_personal_calendar_data_manager.class.php';

class PersonalCalendarPublicationRSS extends PublicationRSS
{

    function __construct()
    {
        parent :: __construct('Chamilo Personal Calendar', htmlspecialchars(Path :: get(WEB_PATH)), 'Personal calendar publications', htmlspecialchars(Path :: get(WEB_PATH)));
    }

    function retrieve_items($user, $min_date = '')
    {
        $pubs = PersonalCalendarDataManager :: get_instance()->retrieve_calendar_event_publications(null, null, 20); //, array('id', SORT_DESC));
        $publications = array();
        while ($pub = $pubs->next_result())
        {
            if ($this->is_visible_for_user($user, $pub))
            {
                $publications[] = $pub;
            }
        }
        return $publications;
    }

    function add_item($publication, $channel)
    {
        $co = $publication->get_content_object_id();
        if (! is_object($co))
        {
            $co = RepositoryDataManager :: get_instance()->retrieve_content_object($co);
        }
        $channel->add_item(htmlspecialchars($co->get_title()), htmlspecialchars($this->get_url($publication)), htmlspecialchars($co->get_description()));
    }

    function get_url($pub)
    {
        $params[Application :: PARAM_ACTION] = PersonalCalendarManager :: ACTION_VIEW_PUBLICATION;
        $params[PersonalCalendarManager :: PARAM_PERSONAL_CALENDAR_ID] = $pub->get_id();
        return Path :: get(WEB_PATH) . Redirect :: get_link(PersonalCalendarManager :: APPLICATION_NAME, $params);
    }

    function is_visible_for_user($user, $pub)
    {
        return ($pub->is_target($user) || $user->get_id() == $pub->get_publisher());
    }
}

?>
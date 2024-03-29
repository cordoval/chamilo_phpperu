<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\libraries\ActionBarSearchForm;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\Session;
use common\libraries\PlatformSetting;
use common\libraries\ArrayResultSet;

use common\extensions\video_conferencing_manager\VideoConferencingManagerConnector;
use common\extensions\video_conferencing_manager\VideoConferencingObject;
use common\extensions\video_conferencing_manager\VideoConferencingRights;

use repository\ExternalSetting;
use repository\ExternalSync;
use repository\content_object\bbb_meeting\BbbMeeting;

use user\UserDataManager;

use phpBbb;

require_once Path :: get_plugin_path(__NAMESPACE__) . 'phpbbb/bbb.php';

/**
 * server : http://192.168.0.162
 * security_salt : 6343dabde830897ffefdf2e9ac3e0a9c
 */

class BbbVideoConferencingManagerConnector extends VideoConferencingManagerConnector
{
    private $bbb;

    /**
     * @param ExternalRepository $external_repository_instance
     */
    function __construct($video_conferencing_instance)
    {
        parent :: __construct($video_conferencing_instance);

        $server = ExternalSetting :: get('server', $this->get_video_conferencing_instance_id());
        $security_salt = ExternalSetting :: get('security_salt', $this->get_video_conferencing_instance_id());

        $this->bbb = new phpBbb($server, $security_salt);
    }

    function create_video_conferencing_object(VideoConferencingObject $video_conferencing_object)
    {
        $meeting_id = uniqid();
        $response = $this->bbb->create_meeting($video_conferencing_object->get_title(), $meeting_id, $video_conferencing_object->get_attendee_pw(), $video_conferencing_object->get_moderator_pw(), $video_conferencing_object->get_welcome(), $video_conferencing_object->get_logout_url(), $video_conferencing_object->get_max_participants());

        if ($response['returncode'] === 'SUCCESS')
        {
            $video_conferencing_object->set_video_conferencing_id($this->get_video_conferencing_instance_id());
            $video_conferencing_object->set_id($response['meetingID']);
            $video_conferencing_object->set_attendee_pw($response['attendeePW']);
            $video_conferencing_object->set_moderator_pw($response['moderatorPW']);

            $bbb_meeting = new BbbMeeting();
            $bbb_meeting->set_title($video_conferencing_object->get_title());
            $bbb_meeting->set_moderator_pw($video_conferencing_object->get_moderator_pw());

            $bbb_meeting->set_owner_id(Session :: get_user_id());

            if (PlatformSetting :: get('description_required', 'repository'))
            {
                $bbb_meeting->set_description('-');
            }

            if (! $bbb_meeting->create())
            {
                return false;
            }
            else
            {
                ExternalSync :: quicksave($bbb_meeting, $video_conferencing_object, $this->get_video_conferencing_instance()->get_id());
            }

            return $bbb_meeting;
        }
        else
        {
            return $response['message'];
        }
    }

    function retrieve_video_conferencing_objects($condition, $order_property, $offset, $count)
    {
        $response = $this->bbb->get_meetings();
        if ($response['returncode'] === 'SUCCESS')
        {
            $meetings = array();
            foreach ($response['meetings']['meeting'] as $meeting)
            {
                $video_conferencing_object = new BbbVideoConferencingObject();
                $video_conferencing_object->set_video_conferencing_id($this->get_video_conferencing_instance_id());
                $video_conferencing_object->set_title($meeting['meetingID']);
                $video_conferencing_object->set_id($meeting['meetingID']);
                $video_conferencing_object->set_attendee_pw($meeting['attendeePW']);
                $video_conferencing_object->set_moderator_pw($meeting['moderatorPW']);
                $video_conferencing_object->set_running($meeting['running']);

                $meetings[] = $video_conferencing_object;
            }
            return new ArrayResultSet($meetings);
        }
        else
        {
            return new ArrayResultSet(array());
        }
    }

    function count_video_conferencing_objects($condition)
    {
        $response = $this->bbb->get_meetings();
        if ($response['returncode'] === 'SUCCESS')
        {
            return count($response['meetings']['meeting']);
        }
        else
        {
            return 0;
        }
    }

    function retrieve_video_conferencing_object($external_sync)
    {
        $response = $this->bbb->get_meeting_info($external_sync->get_external_object_id(), $external_sync->get_content_object()->get_moderator_pw());

        if ($response['returncode'] === 'SUCCESS')
        {

            $video_conferencing_object = new BbbVideoConferencingObject();
            $video_conferencing_object->set_video_conferencing_id($this->get_video_conferencing_instance_id());
            $video_conferencing_object->set_title($response['meetingID']);
            $video_conferencing_object->set_id($response['meetingID']);
            $video_conferencing_object->set_attendee_pw($response['attendeePW']);
            $video_conferencing_object->set_moderator_pw($response['moderatorPW']);
            $video_conferencing_object->set_running($response['running']);
            $video_conferencing_object->set_start_time($response['startTime']);
            $video_conferencing_object->set_end_time($response['endTime']);
            $video_conferencing_object->set_forcibly_ended($response['hasBeenForciblyEnded']);

            foreach ($response['attendees'] as $attendee)
            {
                if ($attendee['role'] === 'MODERATOR')
                {
                    $video_conferencing_object->add_moderator($attendee);
                }
                else
                {
                    $video_conferencing_object->add_viewer($attendee);
                }

            }

            return $video_conferencing_object;
        }
        return false;
    }

    function join_video_conferencing_object(ExternalSync $external_sync, VideoConferencingRights $rights)
    {
        $object = $external_sync->get_external_object();
        $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());

        if ($rights->is_moderator())
        {
            $password = $object->get_moderator_pw();
        }
        else
        {
            $password = $object->get_attendee_pw();
        }
        return $this->bbb->join_meeting($user->get_fullname(), $object->get_id(), $password);
    }

    function end_video_conferencing_object(ExternalSync $external_sync)
    {
        $object = $external_sync->get_external_object();
        $user = UserDataManager :: get_instance()->retrieve_user(Session :: get_user_id());
        return $this->bbb->end_meeting($object->get_id(), $object->get_moderator_pw());
    }

    /**
     * @param int $instance_id
     * @return VimeoExternalRepositoryManagerConnector:
     */
    static function get_instance($instance_id)
    {
        if (! isset(self :: $instance[$instance_id]))
        {
            self :: $instance[$instance_id] = new BbbVideoConferencingManagerConnector($instance_id);
        }
        return self :: $instance[$instance_id];
    }

    /**
     * @param string $query
     * @return string
     */
    static function translate_search_query($query)
    {
        return $query;
    }

    /**
     * @param ObjectTableOrder $order_properties
     * @return string|null
     */
    function convert_order_property($order_properties)
    {
        if (count($order_properties) > 0)
        {
            $order_property = $order_properties[0]->get_property();
            if ($order_property == self :: SORT_RELEVANCE)
            {
                return $order_property;
            }
            else
            {
                $sorting_direction = $order_properties[0]->get_direction();

                if ($sorting_direction == SORT_ASC)
                {
                    return $order_property . '-asc';
                }
                elseif ($sorting_direction == SORT_DESC)
                {
                    return $order_property . '-desc';
                }
            }
        }

        return null;
    }

    /**
     * @return array
     */
    static function get_sort_properties()
    {
        $feed_type = Request :: get(BbbVideoConferencingManager :: PARAM_FEED_TYPE);
        $query = ActionBarSearchForm :: get_query();

        if (($feed_type == BbbVideoConferencingManager :: FEED_TYPE_GENERAL && $query) || $feed_type == BbbVideoConferencingManager :: FEED_TYPE_MY_PHOTOS)
        {
            return array(
                    self :: SORT_DATE_POSTED,
                    self :: SORT_DATE_TAKEN,
                    self :: SORT_INTERESTINGNESS,
                    self :: SORT_RELEVANCE);
        }
        else
        {
            return array();
        }

    }

    /**
     * @param int $license
     * @param string $photo_user_id
     * @return boolean
     */
    function determine_rights($video_entry)
    {
        $rights = array();
        $rights[VideoConferencingObject :: RIGHT_USE] = true;
        $rights[VideoConferencingObject :: RIGHT_EDIT] = true;
        $rights[VideoConferencingObject :: RIGHT_DELETE] = true;
        $rights[VideoConferencingObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }

    function delete_video_conferencing_object($id)
    {
    }

    function export_video_conferencing_object($id)
    {
    }

}
?>
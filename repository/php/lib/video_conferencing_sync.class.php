<?php
namespace repository;

use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\StringUtilities;
use common\extensions\video_conferencing_manager\VideoConferencingConnector;

/**
 * @author Hans De Bisschop
 *
 */
class VideoConferencingSync extends RepositoryDataClass
{
    const CLASS_NAME = __CLASS__;

    const PROPERTY_CONTENT_OBJECT_ID = 'content_object_id';
    const PROPERTY_CONTENT_OBJECT_TIMESTAMP = 'content_object_timestamp';

    const PROPERTY_VIDEO_CONFERENCING_ID = 'video_conferencing_id';
    const PROPERTY_VIDEO_CONFERENCING_OBJECT_ID = 'video_conferencing_object_id';
    const PROPERTY_VIDEO_CONFERENCING_OBJECT_TIMESTAMP = 'video_conferencing_object_timestamp';

    const SYNC_STATUS_ERROR = 0;
    const SYNC_STATUS_EXTERNAL = 1;
    const SYNC_STATUS_INTERNAL = 2;
    const SYNC_STATUS_IDENTICAL = 3;
    const SYNC_STATUS_CONFLICT = 4;

    /**
     * @var ContentObject
     */
    private $content_object;

    /**
     * @var ExternalRepositoryObject
     */
    private $video_conferencing_object;

    /**
     * @var int
     */
    private $synchronization_status;

    /**
     * @var ExternalRepository
     */
    private $video_conferencing;

    /**
     * @param int $content_object_id
     */
    function set_content_object_id($content_object_id)
    {
        if (isset($content_object_id) && is_numeric($content_object_id))
        {
            $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        }
    }

    /**
     * @return int
     */
    function get_content_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_ID);
    }

    /**
     * @param string $external_repository_object_id
     */
    function set_video_conferencing_object_id($video_conferencing_object_id)
    {
        if (StringUtilities :: has_value($video_conferencing_object_id))
        {
            $this->set_default_property(self :: PROPERTY_VIDEO_CONFERENCING_OBJECT_ID, $video_conferencing_object_id);
        }
    }

    /**
     * @return string
     */
    function get_video_conferencing_object_id()
    {
        return $this->get_default_property(self :: PROPERTY_VIDEO_CONFERENCING_OBJECT_ID);
    }

    /**
     * @param int $datetime
     */
    function set_content_object_timestamp($datetime)
    {
        if (isset($datetime) && is_numeric($datetime))
        {
            $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT_TIMESTAMP, $datetime);
        }
    }

    /**
     * @return int
     */
    function get_content_object_timestamp()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT_TIMESTAMP);
    }

    /**
     * @param int $datetime
     */
    function set_video_conferencing_object_timestamp($datetime)
    {
        if (isset($datetime) && is_numeric($datetime))
        {
            $this->set_default_property(self :: PROPERTY_VIDEO_CONFERENCING_OBJECT_TIMESTAMP, $datetime);
        }
    }

    /**
     * @return int
     */
    function get_video_conferencing_object_timestamp()
    {
        return $this->get_default_property(self :: PROPERTY_VIDEO_CONFERENCING_OBJECT_TIMESTAMP);
    }

    /**
     * @param int $external_repository_id
     */
    function set_video_conferencing_id($video_conferencing_id)
    {
        if (isset($video_conferencing_id) && is_numeric($video_conferencing_id))
        {
            $this->set_default_property(self :: PROPERTY_VIDEO_CONFERENCING_ID, $video_conferencing_id);
        }
    }

    /**
     * @return int
     */
    function get_video_conferencing_id()
    {
        return $this->get_default_property(self :: PROPERTY_VIDEO_CONFERENCING_ID);
    }

    /**
     * @param array $property_names
     */
    static function get_default_property_names($extended_property_names = array())
    {
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_ID;
        $extended_property_names[] = self :: PROPERTY_CONTENT_OBJECT_TIMESTAMP;
        $extended_property_names[] = self :: PROPERTY_VIDEO_CONFERENCING_ID;
        $extended_property_names[] = self :: PROPERTY_VIDEO_CONFERENCING_OBJECT_ID;
        $extended_property_names[] = self :: PROPERTY_VIDEO_CONFERENCING_OBJECT_TIMESTAMP;

        return parent :: get_default_property_names($extended_property_names);
    }

    /**
     * @return string
     */
    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    /**
     * @return boolean
     */
    function create()
    {
        $now = time();
        $this->set_creation_date($now);
        $this->set_modification_date($now);
        return parent :: create();
    }

    /**
     * @return boolean
     */
    function update()
    {
        if (! $this->is_identified())
        {
            throw new Exception('VideoConferencingSync object could not be saved as its identity is not set');
        }

        $this->set_modification_date(time());
        return parent :: update();
    }

    /**
     * @return ContentObject
     */
    function get_content_object()
    {
        if (! isset($this->content_object))
        {
            $this->content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_content_object_id());
        }
        return $this->content_object;
    }

    /**
     * @return ExternalRepositoryObject
     */
    function get_video_conferencing_object()
    {
        if (! isset($this->video_conferencing_object))
        {
            $video_conferencing_instance = $this->get_video_conferencing();
            $this->video_conferencing_object = VideoConferencingConnector :: get_instance($video_conferencing_instance)->retrieve_video_conferencing_object($this->get_video_conferencing_object_id());
        }
        return $this->video_conferencing_object;
    }

    /**
     * @return ExternalRepository
     */
    function get_video_conferencing()
    {
        if (! isset($this->video_conferencing))
        {
            $this->video_conferencing = RepositoryDataManager :: get_instance()->retrieve_video_conferencing($this->get_video_conferencing_id());
        }
        return $this->video_conferencing;
    }

    function get_synchronization_status($content_object_date = null, $external_object_date = null)
    {
        if (! isset($this->synchronization_status))
        {
            if (is_null($content_object_date))
            {
                $content_object_date = $this->get_content_object()->get_modification_date();
            }

            if (is_null($external_object_date))
            {
                $external_object_date = $this->get_video_conferencing_object()->get_created();
            }

            if ($content_object_date > $this->get_content_object_timestamp())
            {
                if ($external_object_date > $this->get_video_conferencing_object_timestamp())
                {
                    $this->synchronization_status = self :: SYNC_STATUS_CONFLICT;
                }
                elseif ($external_object_date == $this->get_video_conferencing_object_timestamp())
                {
                    $this->synchronization_status = self :: SYNC_STATUS_EXTERNAL;
                }
                else
                {
                    $this->synchronization_status = self :: SYNC_STATUS_ERROR;
                }
            }
            elseif ($content_object_date == $this->get_content_object_timestamp())
            {
                if ($external_object_date > $this->get_video_conferencing_object_timestamp())
                {
                    $this->synchronization_status = self :: SYNC_STATUS_INTERNAL;
                }
                elseif ($external_object_date == $this->get_video_conferencing_object_timestamp())
                {
                    $this->synchronization_status = self :: SYNC_STATUS_IDENTICAL;
                }
                else
                {
                    $this->synchronization_status = self :: SYNC_STATUS_ERROR;
                }
            }
            else
            {
                $this->synchronization_status = self :: SYNC_STATUS_ERROR;
            }
        }

        return $this->synchronization_status;
    }

    /*************************************************************************
     * Fat model methods
     *************************************************************************/

    /**
     * @param int $content_object_id
     * @return ExternalRepositorySync
     */
    public static function get_by_content_object_id($content_object_id)
    {
        $conditions = new EqualityCondition(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        return $this->get_data_manager()->retrieve_video_conferencing_sync($conditions);
    }

    /**
     * @param int $content_object_id
     * @param int $external_repository_id
     * @return ExternalRepositorySync
     */
    public static function get_by_content_object_id_and_video_conferencing_id($content_object_id, $video_conferencing_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(self :: PROPERTY_CONTENT_OBJECT_ID, $content_object_id);
        $conditions[] = new EqualityCondition(self :: PROPERTY_VIDEO_CONFERENCING_ID, $video_conferencing_id);
        $condition = new AndCondition($conditions);
        return $this->get_data_manager()->retrieve_video_conferencing_sync($condition);
    }

    /**
     * @param int $external_repository_object_id
     * @param int $external_repository_id
     * @return ExternalRepositorySync
     */
    public static function get_by_external_repository_object_id_and_video_conferencing_id($video_conferencing_object_id, $video_conferencing_id)
    {
        $conditions = array();
        $conditions[] = new EqualityCondition(self :: PROPERTY_VIDEO_CONFERENCING_OBJECT_ID, $video_conferencing_object_id);
        $conditions[] = new EqualityCondition(self :: PROPERTY_VIDEO_CONFERENCING_ID, $video_conferencing_id);
        $condition = new AndCondition($conditions);
        return $this->get_data_manager()->retrieve_video_conferencing_sync($conditions);
    }

    /**
     * @param ContentObject $content_object
     * @param ExternalRepositoryObject $external_repository_object
     * @param int $external_repository_id
     * @return boolean
     */
    public static function quicksave(ContentObject $content_object, VideoConferencingObject $video_conferencing_object, $video_conferencing_id)
    {
        $sync = new VideoConferencingSync();
        $sync->set_content_object_id($content_object->get_id());
        $sync->set_content_object_timestamp($content_object->get_modification_date());
        $sync->set_video_conferencing_id((int) $video_conferencing_id);
        $sync->set_video_conferencing_object_id((string) $video_conferencing_object->get_id());
        $sync->set_video_conferencing_object_timestamp($video_conferencing_object->get_modified());
        return $sync->create();
    }

}
?>
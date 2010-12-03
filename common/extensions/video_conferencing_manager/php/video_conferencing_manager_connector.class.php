<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Utilities;

use repository\ExternalSync;
use Exception;

abstract class VideoConferencingManagerConnector
{

    private static $instances = array();

    private $video_conferencing_instance;
    
    /**
     * @param VideoConferencing $video_conferencing_instance
     */
    function __construct($video_conferencing_instance)
    {
        $this->video_conferencing_instance = $video_conferencing_instance;
    }
    
    /**
     * @return VideoConferencing
     */
    function get_video_conferencing_instance()
    {
        return $this->video_conferencing_instance;
    }
    
    /**
     * @param VideoConferencing $video_conferencing_instance
     */
    function set_video_conferencing_instance($video_conferencing_instance)
    {
        $this->video_conferencing_instance = $video_conferencing_instance;
    }
    
    /**
     * @return int
     */
    function get_video_conferencing_instance_id()
    {
        return $this->get_video_conferencing_instance()->get_id();
    }
    
    /**
     * @param VideoConferencing $video_conferencing_instance
     * @return VideoConferencingManagerConnector
     */
    static function factory($video_conferencing_instance)
    {
        $type = $video_conferencing_instance->get_type();
        
        $file = dirname(__FILE__) . '/../implementation/' . $type . '/php/' . $type . '_video_conferencing_manager_connector.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('VideoConferencingManagerConnectorTypeDoesNotExist', array('type' => $type)));
        }
        
        require_once $file;
        
        $class ='common\extensions\video_conferencing_manager\implementation\\' . $type .'\\'  . Utilities :: underscores_to_camelcase($type) . 'VideoConferencingManagerConnector';
        return new $class($video_conferencing_instance);
    }
    
    /**
     * @param VideoConferencing $video_conferencing_instance
     * @return VideoConferencingManagerConnector
     */
    static function get_instance($video_conferencing_instance)
    {
        if (! isset(self :: $instances[$video_conferencing_instance->get_id()]))
        {
            self :: $instances[$video_conferencing_instance->get_id()] = self :: factory($video_conferencing_instance);
        }
        return self :: $instances[$video_conferencing_instance->get_id()];
    }

    /**
     * @param string $id
     */
    abstract function retrieve_video_conferencing_object($external_sync);

    function retrieve_external_object(ExternalSync $external_sync)
    {
    	return $this->retrieve_video_conferencing_object($external_sync);
    }

    /**
     * @param mixed $condition
     * @param ObjectTableOrder $order_property
     * @param int $offset
     * @param int $count
     */
    abstract function retrieve_video_conferencing_objects($condition, $order_property, $offset, $count);

    abstract function create_video_conferencing_object(VideoConferencingObject $video_conferencing_object);
    
    abstract function join_video_conferencing_object(ExternalSync $external_sync, VideoConferencingRights $rights);
    
    /**
     * @param mixed $condition
     */
    abstract function count_video_conferencing_objects($condition);

    /**
     * @param string $id
     */
    abstract function delete_video_conferencing_object($id);

    /**
     * @param string $id
     */
    abstract function export_video_conferencing_object($id);

    /**
     * @param string $query
     */
    abstract static function translate_search_query($query);
}
?>
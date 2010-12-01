<?php
namespace common\extensions\video_conferencing_manager\implementation\bbb;

use common\extensions\video_conferencing_manager;

use common\libraries\Path;
use common\libraries\Session;
use common\libraries\PlatformSetting;

use common\extensions\video_conferencing_manager\VideoConferencingConnector;
use common\extensions\video_conferencing_manager\VideoConferencingObject;

use repository\ExternalSetting;
use repository\ExternalSync;
use repository\content_object\bbb_meeting\BbbMeeting;

use phpBbb;

require_once Path :: get_plugin_path(__NAMESPACE__) . 'phpbbb/bbb.php';

class BbbVideoConferencingConnector extends VideoConferencingConnector
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

        
        
		//$this->bbb->is_meeting_running('test123');
		
		//$this->bbb->get_metting_info('test');
  
    }
    
    function create_video_conferencing_object(VideoConferencingObject $video_conferencing_object)
    {
    	$meeting_id = uniqid();
    	$response = $this->bbb->create_meeting($video_conferencing_object->get_title(), $meeting_id , $video_conferencing_object->get_attendee_pw(), $video_conferencing_object->get_moderator_pw(), $video_conferencing_object->get_welcome(), $video_conferencing_object->get_logout_url(), $video_conferencing_object->get_max_participants());
    	if ($response['result'] === true)
    	{
    		$video_conferencing_object->set_id($reponse['meeting_id']);
    		$video_conferencing_object->set_attendee_pw($reponse['attendee_pw']);
    		$video_conferencing_object->set_moderator_pw($reponse['moderator_pw']);

    		$bbb_meeting = new BbbMeeting();
    		$bbb_meeting->set_title($video_conferencing_object->get_title());
    		
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
    		
    		return $video_conferencing_object;
    	}
    	else 
    	{
    		return $response['message'];
    	}	
    }
    
    function retrieve_video_conferencing_objects($condition, $order_property, $offset, $count)
    {
    	$response = $this->bbb->get_meetings();
    	    	
    }
    
	function retrieve_video_conferencing_object($video_conferencing_object)
    {
    	$response = $this->bbb->get_metting_info($video_conferencing_object->get_id(), $video_conferencing->get_moderator_pw());
    }
    
    function join_video_conferencing_object(VideoConferencingObject $video_conferencing_object)
    {
    	
    	$this->bbb->join_meeting('Gillard Magali', $video_conferencing_object->meeting_id, 'test');
    }
    /**
     * @param int $instance_id
     * @return VimeoExternalRepositoryConnector:
     */
    static function get_instance($instance_id)
    {
        if (! isset(self :: $instance[$instance_id]))
        {
            self :: $instance[$instance_id] = new VimeoExternalRepositoryConnector($instance_id);
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
        $feed_type = Request :: get(VimeoExternalRepositoryManager :: PARAM_FEED_TYPE);
        $query = ActionBarSearchForm :: get_query();
        
        if (($feed_type == VimeoExternalRepositoryManager :: FEED_TYPE_GENERAL && $query) || $feed_type == VimeoExternalRepositoryManager :: FEED_TYPE_MY_PHOTOS)
        {
            return array(self :: SORT_DATE_POSTED, self :: SORT_DATE_TAKEN, self :: SORT_INTERESTINGNESS, self :: SORT_RELEVANCE);
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
        $rights[ExternalRepositoryObject :: RIGHT_USE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_EDIT] = true;
        $rights[ExternalRepositoryObject :: RIGHT_DELETE] = true;
        $rights[ExternalRepositoryObject :: RIGHT_DOWNLOAD] = false;
        return $rights;
    }

//    public function retrieve_video_conferencing_object($id) {
//        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
//    }
//    public function retrieve_video_conferencing_objects($condition, $order_property, $offset, $count) {
//        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
//    }
//    public function count_video_conferencing_objects($condition) {
//        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
//    }
//    public function delete_video_conferencing_object($id) {
//        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
//    }
//    public function export_video_conferencing_object($id) {
//        throw new Exception("Unimplemented method : " . __METHOD__ . " :=> " . __FILE__ . ":" . __LINE__);
//    }

    

    function count_video_conferencing_objects($condition)
    {}

    function delete_video_conferencing_object($id)
    {}

	function export_video_conferencing_object($id)
	{}

}
?>
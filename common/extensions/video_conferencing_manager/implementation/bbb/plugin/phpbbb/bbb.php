<?php
class phpBbb
{
    const API_CREATE = '/bigbluebutton/api/create?';
    const API_IS_MEETING_RUNNING = '/bigbluebutton/api/isMeetingRunning?';
    const API_JOIN_MEETING = '/bigbluebutton/api/join?';
    const API_GET_MEETING_INFO = '/bigbluebutton/api/getMeetingInfo?';
    
    private $meeting_id;
    private $ip;
    private $security_salt;

    function __construct($ip, $security_salt)
    {
        $this->ip = $ip;
        $this->security_salt = $security_salt;
    }

    function authenticate($account_id, $account_pw)
    {
        echo ('authenticate');
    }

    function create_meeting($meeting_name, $meeting_id, $attendee_pw, $moderator_pw, $welcome_message)
    {
//        if (! $this->is_meeting_running($meeting_id))
//        {
            $this->meeting_id = $meeting_id;
            $voice_bridge = rand(70000, 79999);
            $construct_url = 'name=' . urlencode($meeting_name) . '&meetingID=' . $this->meeting_id . '&attendeePW=' . $attendee_pw . '&moderatorPW=' . $moderator_pw /*. '&voiceBrigde=' . $voice_bridge . '&welcome' . $welcome_message . '&logoutURL=' . $logout_url*/;
            $checksum = sha1('create' . $construct_url . $this->security_salt);
            $create_url = 'http://' . $this->ip . self :: API_CREATE . $construct_url . '&checksum=' . $checksum;
            dump($create_url);
            $response = file_get_contents($create_url);
            dump($response);
            
//            $doc = new DOMDocument();
//            $doc->loadXML($response);
//            $returnCodeNode = $doc->getElementsByTagName("returncode");
//            $returnCode = $returnCodeNode->item(0)->nodeValue;
//            
//            if ($returnCode == "SUCCESS")
//            {
//                return $returnCode;
//            }
//            else
//            {
//                $messageKeyNode = $doc->getElementsByTagName("messageKey");
//                $messageKey = $messageKeyNode->item(0)->nodeValue;
//                return $messageKey;
//            }
//        }
    }

    function is_meeting_running($meeting_id)
    {
        $construct_url = 'meetingID=' . $meeting_id;
        $checksum = sha1('isMeetingRunning' . $construct_url . $this->security_salt);

        $is_running_url = 'http://' . $this->ip . self :: API_IS_MEETING_RUNNING . $construct_url . '&checksum=' . $checksum;
        $response = file_get_contents($is_running_url);
        dump($response);
        
        $doc = new DOMDocument();
        $doc->loadXML($response);
        $returnCodeNode = $doc->getElementsByTagName("returncode");
        $returnCode = $returnCodeNode->item(0)->nodeValue;
        
        if ($returnCode == "SUCCESS")
        {
            return $returnCode;
        }
        else
        {
            $messageKeyNode = $doc->getElementsByTagName("messageKey");
            $messageKey = $messageKeyNode->item(0)->nodeValue;
            return $messageKey;
        }
    
    }

    function join_meeting($name, $password)
    {
        $construct_url = 'fullName=' . urlencode($name) . '&meetingID=' . $this->meeting_id . '&password=' . $password;
        $checksum = sha1('join' . $construct_url . $this->security_salt);
        
        $is_running_url = 'http://' . $this->ip . self :: API_JOIN_MEETING . '&checksum=' . $checksum;
        $response = file_get_contents($is_running_url);
    
    }

    function get_meetings()
    {
    
    }

    function get_metting_info($password)
    {
        $construct_url = 'meetingID=' . $this->meeting_id . '&password=' . $password;
        $checksum = sha1('getMeetingInfo' . $construct_url . $this->security_salt);
        
        $is_running_url = 'http://' . $this->ip . self :: API_GET_MEETING_INFO . '&checksum=' . $checksum;
        $response = file_get_contents($is_running_url);
        dump($response);
        
        $doc = new DOMDocument();
        $doc->loadXML($response);
        
        $returnCodeNode = $doc->getElementsByTagName("returncode");
        $returnCode = $returnCodeNode->item(0)->nodeValue;
        
        if ($returnCode == "SUCCESS")
        {
            return $returnCode;
        }
        else
        {
            $messageKeyNode = $doc->getElementsByTagName("messageKey");
            $messageKey = $messageKeyNode->item(0)->nodeValue;
            return $messageKey;
        }
    }

    function end_meeting()
    {
    
    }

    function list_attendees()
    {
    
    }
}
?>
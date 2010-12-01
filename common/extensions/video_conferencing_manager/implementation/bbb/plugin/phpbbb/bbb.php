<?php
class phpBbb
{
    const API_CREATE = '/bigbluebutton/api/create?';
    const API_IS_MEETING_RUNNING = '/bigbluebutton/api/isMeetingRunning?';
    const API_JOIN_MEETING = '/bigbluebutton/api/join?';
    const API_GET_MEETING_INFO = '/bigbluebutton/api/getMeetingInfo?';
    const API_GET_MEETINGS = '/bigbluebutton/api/getMeetings?';
    const API_END_MEETING = '/bigbluebutton/api/end?';
    
    private $meeting_id;
    private $ip;
    private $security_salt;

    function __construct($ip, $security_salt)
    {
        $this->ip = $ip;
        $this->security_salt = $security_salt;
    }

    function create_meeting($meeting_name, $meeting_id, $attendee_pw = null, $moderator_pw = null, $welcome_message = null, $logout_url = null, $max_participants = null)
    {
        //        if (! $this->is_meeting_running($meeting_id))
        //        {
        $this->meeting_id = $meeting_id;
        
        $parameters = array();
        $parameters['name'] = urlencode($meeting_name);
        $parameters['meetingID'] = $this->meeting_id;
        $parameters['attendeePW'] = $attendee_pw;
        $parameters['moderatorPW'] = $moderator_pw;
        $parameters['welcome'] = $welcome_message;
        $parameters['logoutURL'] = $logout_url;
        $parameters['maxParticipants'] = $max_participants;
        
        $construct_url = http_build_query($parameters);
        $checksum = sha1('create' . $construct_url . $this->security_salt);
        $create_url = $this->ip . self :: API_CREATE . $construct_url . '&checksum=' . $checksum;
        
        $response = file_get_contents($create_url);
        
        $doc = new DOMDocument();
        $doc->loadXML($response);
        $returnCodeNode = $doc->getElementsByTagName("returncode");
        $returnCode = $returnCodeNode->item(0)->nodeValue;
        
        $unserializer = new XML_Unserializer();
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
        
        // userialize the document
        $status = $unserializer->unserialize($response);
        
        if (PEAR :: isError($status))
        {
            $this->display_error_page($status->getMessage());
        }
        else
        {
            return $unserializer->getUnserializedData();
        }
        //        }
    }

    function is_meeting_running($meeting_id)
    {
        $construct_url = 'meetingID=' . $meeting_id;
        $checksum = sha1('isMeetingRunning' . $construct_url . $this->security_salt);
        
        $is_running_url = $this->ip . self :: API_IS_MEETING_RUNNING . $construct_url . '&checksum=' . $checksum;
        $response = file_get_contents($is_running_url);
        
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
        
        $is_running_url = $this->ip . self :: API_JOIN_MEETING . '&checksum=' . $checksum;
        $response = file_get_contents($is_running_url);
    
    }

    function get_meetings()
    {
        $random = rand();
        $construct_url = 'random=' . $random;
        $checksum = sha1('getMeetings' . $construct_url . $this->security_salt);
        
        $get_meetings_url = $this->ip . self :: API_GET_MEETINGS . $construct_url . '&checksum=' . $checksum;
        $response = file_get_contents($get_meetings_url);
        
        $doc = new DOMDocument();
        $doc->loadXML($response);
        $returnCodeNode = $doc->getElementsByTagName("returncode");
        $returnCode = $returnCodeNode->item(0)->nodeValue;
        
        if ($returnCode == "SUCCESS")
        {
            $response = array();
            $response['meeting_id'] = $doc->getElementsByTagName("meetingID")->item(0)->nodeValue;
            $response['attendee_pw'] = $doc->getElementsByTagName("attendeePw")->item(0)->nodeValue;
            $response['moderator_pw'] = $doc->getElementsByTagName("moderatorPw")->item(0)->nodeValue;
            $response['status'] = $doc->getElementsByTagName("running")->item(0)->nodeValue;
            $response['result'] = true;
        }
        else
        {
            $response = array();
            $response['result'] = false;
            $response['message'] = $doc->getElementsByTagName("messageKey")->item(0)->nodeValue;
        
        }
        return $response;
    }

    function get_meeting_info($meeting_id, $password)
    {
        $parameters = array();
        $parameters['meetingID'] = $meeting_id;
        $parameters['password'] = $password;
        $construct_url = http_build_query($parameters);
        
        $checksum = sha1('getMeetingInfo' . $construct_url . $this->security_salt);
        
        $is_running_url = $this->ip . self :: API_GET_MEETING_INFO . $construct_url . '&checksum=' . $checksum;
        $response = file_get_contents($is_running_url);
        
        $unserializer = new XML_Unserializer();
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
        $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('attendee'));
        
        // userialize the document
        $status = $unserializer->unserialize($response);
        
        if (PEAR :: isError($status))
        {
            $this->display_error_page($status->getMessage());
        }
        else
        {
            return $unserializer->getUnserializedData();
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
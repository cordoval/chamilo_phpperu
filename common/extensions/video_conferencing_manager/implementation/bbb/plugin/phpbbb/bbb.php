<?php
class phpBbb
{
	const API_CREATE = '/bigbluebutton/api/create?';
	
	private $account_id;
	private $account_pw;
	private $meeting_id;
	private $meeting_name;
	
	function __construct($ip, $security)
	{
		$this->ip = $ip;
		$this->$security = $security;
	}
	
	function authenticate($meeting_name, $meeting_id, $attendee_pw, $moderator_pw, $welcome_message)
	{
		$voice_bridge = rand(70000,79999);
		$construct_url = 'name' . $meeting_name . '&meetingID=' . $meeting_id . '&attendeePW=' . $attendee_pw . '&moderatorPW=' . $moderator_pw . '&voiceBrigde=' . $voice_bridge . '&welcome' . $welcome_message . '&logoutURL=' . $logout_url;
		$checksum = sha1('create' . $construct_url . $this->securrity);
		$authenticate_url = 'http://' . $this->ip . self :: API_CREATE . $query . '&checksum=' . $checksum;
		$response = file_get_contents($authenticate_url);
		dump($response);
	}
	
	function create_meeting($ip )
	{
		
	}
	
	function join_meeting()
	{
		
	}
	
	function get_meetings()
	{
		
	}
	
	function get_metting_info()
	{
		
	}
	
	function is_meeting_running()
	{
		
	}
	
	function end_meeting()
	{
		
	}
	
	function list_attendees()
	{
		
	}
	
//	
//function dc_authenticate($myAccountID,$myAccountPWD) {
//	$authenticateURL = "http://bigbluebutton.dualcode.com/api.php?call=authenticate&accountid=".urlencode($myAccountID)."&accountpwd=".urlencode($myAccountPWD)."&version=070";
//	
//	$myResponse = file_get_contents($authenticateURL);
//	$doc = new DOMDocument();
//	$doc->loadXML($myResponse);
//	$returnCodeNode = $doc->getElementsByTagName("returncode");
//	$returnCode = $returnCodeNode->item(0)->nodeValue;
//
//	if ($returnCode=="SUCCESS") {
//	  $serveridNode = $doc->getElementsByTagName("serverid");
//	  $serverid = $serveridNode->item(0)->nodeValue;	
//	  return $serverid; 
//	}
//	else {
//	  $messageKeyNode = $doc->getElementsByTagName("messageKey");
//	  $messageKey = $messageKeyNode->item(0)->nodeValue;
//	  return $messageKey;
//	}
//}
}
?>
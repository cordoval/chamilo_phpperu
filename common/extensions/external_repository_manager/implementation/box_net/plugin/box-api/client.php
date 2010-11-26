<style type="text/css">
<!--
.style1 {font-family: Georgia, "Times New Roman", Times, serif} A:link {background: none; text-decoration: none; color:#000000;} A:visited {background: none; text-decoration: none; color: #000000;} A:active {background: #333333; font-weight:bold; } A:hover {background: #333333; color:#FFFFFF;} .pic img{ border: 1px solid #ccc;}
-->
</style>

<?php

// --- Important --
// You *must* set your API key in the boxconfig file.
// Set debug value to 'true' on line 30 of boxlib file for return output
// Optional auth_token setup in Box Config file for saved sessions

require 'box_config.php';


// Get Ticket to Proceed

$ticket_return = $box->getTicket ();

if ($box->isError()) {
    echo $box->getErrorMsg();
} else {
	
	$ticket = $ticket_return['ticket'];

}

// Get Friends

$friends = $box->GetFriends ();

// Get Account Tree

$tree = $box->getAccountTree ();

// Add Tag ('tagname', target id, file/folder)

$addtag = $box->AddTag ('tagname', 0, 'folder');

// Public Share (message, emails, id, target_type, password)

for ($i=0, $tree_count; $i<$tree_count; $i++) {
if ($tree['file_name'][$i] != ''){

$publicshare = $box->PublicShare ('This is the message that is e-mailed', 'buglewood@gmail.com', $tree['file_id'][$i], 'file', 'password') ; 

}}


// List files for download


echo " <img src='download.png'/><br/><br/> <div font='georgia' font-size=18px>";

for ($i=0, $tree_count; $i<$tree_count; $i++) {
if ($tree['file_name'][$i] != ''){
	
echo "<table bgcolor='#66cc66'  CELLPADDING='15' border='0' ><TR><TD><strong><a href='http://box.net/api/1.0/download/".$auth_token."/".$tree[file_id][$i]."'>Download ".$tree['file_name'][$i]."</a></TD></TR></table><img src='tail.png'><br/><br/>";

}}


// Upload File
?>

<img src='upload.png'/><br/><br/>

<form action="<? echo $PHP_SELF; ?>" enctype="multipart/form-data" method="POST"><input type="hidden" name="MAX_FILE_SIZE" value="$max_file_size" /><input type="file" name="new_file1" /><input type="submit" name="upload_files" value="Upload File" /></form>

<?

if ($_FILES['new_file1']) {
	
	$upload = $box->UploadFile  ();
	
	
	if ($upload['status'] == 'upload_ok') {
		echo "You've just uploaded ".$upload['file_name']."!";
		
}else{
	echo "Whoops...".$upload['error'];
}}
	?>

<img src='register.png'/><br/><br/> <form action="<? echo $PHP_SELF; ?>" enctype="multipart/form-data" method="POST"> <input type="text" name="login" value="Login" />  <input type="text" name="password" value="Password" /> <input type="submit" name="submit" value="Register" /> </form>

<?

// Register user once form has been filled

if ($_REQUEST['login'] && $_REQUEST['password']) { $reg = $box->RegisterUser  ();
		
	if ($reg['status'] == 'successful_register') {
		echo "You're now logged in as ".$reg['login']."! You've used ".$reg['space_used']." out of ".$reg['space_amount']." available bytes.";
		
		// Save new auth token
		
	  $reg['auth_token'] = $auth_token;
}else{
	echo "Whoops...".$reg['status'];
}}
	?>
	
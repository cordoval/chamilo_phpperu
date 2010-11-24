<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Uploadify scriptData Sample</title>

<link rel="stylesheet" href="uploadify/uploadify.css" type="text/css" />
<link rel="stylesheet" href="css/uploadify.styling.css" type="text/css" />

<script type="text/javascript" src="js/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="js/jquery.uploadify.js"></script>

<script type="text/javascript">

$(document).ready(function() {
	$("#fileUploadstyle").fileUpload({
		'uploader': 'uploadify/uploader.swf',
		'cancelImg': 'uploadify/cancel.png',
		'script': 'uploadify/upload.php',
		'folder': 'files',
		'multi': true,
		'displayData': 'speed',
		'buttonImg': 'css/images/browseBtn.png',
		'width': 80,
		'height': 24,
		'rollover': true
	});

	$("#fileUploadstyle2").fileUpload({
		'uploader': 'uploadify/uploader.swf',
		'cancelImg': 'uploadify/cancel.png',
		'script': 'uploadify/upload.php',
		'folder': 'files',
		'multi': true,
		'simUploadLimit': 2,
		'hideButton': true,
		'width': 80,
		'height': 24
	});
});

</script>
</head>

<body>
      <fieldset style="border: 1px solid #CDCDCD; padding: 8px; padding-bottom:0px; margin: 8px 0">
		<legend><strong>Uploadify - Custom Styling Sample</strong></legend>
		<p>Button Image Override, CSS style to Queue container</p>
		<div id="fileUploadstyle">You have a problem with your javascript</div>
		<a href="javascript:$('#fileUploadstyle').fileUploadStart()">Start Upload</a> |  <a href="javascript:$('#fileUploadstyle').fileUploadClearQueue()">Clear Queue</a>
    	<p></p>
<hr width=100% size="1" color="" align="center">
		<p>CSS styled button</p>
		<div id="fileUploadstyle2">You have a problem with your javascript</div>
		<a href="javascript:$('#fileUploadstyle2').fileUploadStart()">Start Upload</a> |  <a href="javascript:$('#fileUploadstyle2').fileUploadClearQueue()">Clear Queue</a>
    	<p></p>
    </fieldset>
</body>
</html>
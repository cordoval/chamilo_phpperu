<?php
/**
 * @package common.html.formvalidator.Element
 */
// $Id: calendar_popup.php 128 2009-11-09 13:13:20Z vanpouckesven $
// including the global init script
require ('../../../global.inc.php');
Translation :: set_application('home');
// the variables for the days and the months
// Defining the shorts for the days
$DaysShort = array(Translation :: get("SundayShort"), Translation :: get("MondayShort"), Translation :: get("TuesdayShort"), Translation :: get("WednesdayShort"), Translation :: get("ThursdayShort"), Translation :: get("FridayShort"), Translation :: get("SaturdayShort"));
// Defining the days of the week to allow translation of the days
$DaysLong = array(Translation :: get("SundayLong"), Translation :: get("MondayLong"), Translation :: get("TuesdayLong"), Translation :: get("WednesdayLong"), Translation :: get("ThursdayLong"), Translation :: get("FridayLong"), Translation :: get("SaturdayLong"));
// Defining the months of the year to allow translation of the months
$MonthsLong = array(Translation :: get("JanuaryLong"), Translation :: get("FebruaryLong"), Translation :: get("MarchLong"), Translation :: get("AprilLong"), Translation :: get("MayLong"), Translation :: get("JuneLong"), Translation :: get("JulyLong"), Translation :: get("AugustLong"), Translation :: get("SeptemberLong"), Translation :: get("OctoberLong"), Translation :: get("NovemberLong"), Translation :: get("DecemberLong"));

$iso_lang = AdminDataManager :: get_instance()->retrieve_language_from_english_name($language_interface)->get_isocode();
if (empty($document_language))
{
    //if there was no valid iso-code, use the english one
    $iso_lang = 'en';
}
?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
	xml:lang="<?php
echo $iso_lang;
?>" lang="<?php
echo $iso_lang;
?>">
<head>
<title>Calendar</title>
<link rel="stylesheet" type="text/css"
	href="<?php
echo Path :: get(WEB_CODE_PATH);
?>/css/default.css" />
<style type="text/css">
/*<![CDATA[*/
table.calendar {
	width: 100%;
	font-size: 11px;
	font-family: verdana, arial, helvetica, sans-serif;
}

table.calendar .monthyear {
	background-color: #4171B5;
	text-align: center;
	color: #ffffff;
}

table.calendar .daynames {
	background-color: #D3DFF1;
	text-align: center;
}

table.calendar td {
	width: 25px;
	height: 25px;
	background-color: #f5f5f5;
	text-align: center;
}

table.calendar td.selected {
	border: 1px solid #ff0000;
	background-color: #FFCECE;
}

table.calendar td a {
	width: 25px;
	height: 25px;
	text-decoration: none;
}

table.calendar td a:hover {
	background-color: #ffff00;
}

table.calendar .monthyear a {
	text-align: center;
	color: #ffffff;
}

table.calendar .monthyear a:hover {
	text-align: center;
	color: #ff0000;
	background-color: #ffff00;
}
/*]]>*/
</style>
<script type="text/javascript">
/* <![CDATA[ */
    /* added 2004-06-10 by Michael Keck
     *       we need this for Backwards-Compatibility and resolving problems
     *       with non DOM browsers, which may have problems with css 2 (like NC 4)
     */
    var isDOM      = (typeof(document.getElementsByTagName) != 'undefined'
                      && typeof(document.createElement) != 'undefined')
                   ? 1 : 0;
    var isIE4      = (typeof(document.all) != 'undefined'
                      && parseInt(navigator.appVersion) >= 4)
                   ? 1 : 0;
    var isNS4      = (typeof(document.layers) != 'undefined')
                   ? 1 : 0;
    var capable    = (isDOM || isIE4 || isNS4)
                   ? 1 : 0;
    // Uggly fix for Opera and Konqueror 2.2 that are half DOM compliant
    if (capable) {
        if (typeof(window.opera) != 'undefined') {
            var browserName = ' ' + navigator.userAgent.toLowerCase();
            if ((browserName.indexOf('konqueror 7') == 0)) {
                capable = 0;
            }
        } else if (typeof(navigator.userAgent) != 'undefined') {
            var browserName = ' ' + navigator.userAgent.toLowerCase();
            if ((browserName.indexOf('konqueror') > 0) && (browserName.indexOf('konqueror/3') == 0)) {
                capable = 0;
            }
        } // end if... else if...
    } // end if
/* ]]> */
</script>
<script type="text/javascript" src="tbl_change.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
var month_names = new Array(
<?php
foreach ($MonthsLong as $index => $month)
{
    echo '"' . $month . '",';
}
?>
"");
var day_names = new Array(
<?php
foreach ($DaysShort as $index => $day)
{
    echo '"' . $day . '",';
}
?>
"");
/* ]]> */
</script>
</head>
<body onload="initCalendar();">
<div id="calendar_data"></div>
<div id="clock_data"></div>
</body>
</html>

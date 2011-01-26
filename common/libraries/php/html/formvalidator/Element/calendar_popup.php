<?php

namespace common\libraries;

/**
 * @package common.html.formvalidator.Element
 */
// $Id: calendar_popup.php 128 2009-11-09 13:13:20Z vanpouckesven $
// including the global init script
require (dirname(__FILE__) . '/../../../../../global.inc.php');
// the variables for the days and the months
// Defining the shorts for the days
$DaysShort = array(Translation :: get("SundayShort", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("MondayShort", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("TuesdayShort", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("WednesdayShort", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("ThursdayShort", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("FridayShort", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("SaturdayShort", null, Utilities :: COMMON_LIBRARIES));

// Defining the days of the week to allow translation of the days
$DaysLong = array(Translation :: get("SundayLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("MondayLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("TuesdayLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("WednesdayLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("ThursdayLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("FridayLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("SaturdayLong", null, Utilities :: COMMON_LIBRARIES));

// Defining the months of the year to allow translation of the months
$MonthsLong = array(Translation :: get("JanuaryLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("FebruaryLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("MarchLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("AprilLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("MayLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("JuneLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("JulyLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("AugustLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("SeptemberLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("OctoberLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("NovemberLong", null, Utilities :: COMMON_LIBRARIES),
        Translation :: get("DecemberLong", null, Utilities :: COMMON_LIBRARIES));

$iso_lang = Translation :: get_language();
if (empty($document_language))
{
    //if there was no valid iso-code, use the english one
    $iso_lang = 'en';
}
?><!DOCTYPE html
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
<link rel="stylesheet" type="text/css" href="<?php echo Theme :: get_common_css_path(); ?>" />
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
<script type="text/javascript" src="<?php  echo Path :: get(WEB_PATH); ?>common/libraries/plugin/jquery/jquery.min.js"></script>
<script type="text/javascript">
/* <![CDATA[ */
var month_names = new Array(
<?php
foreach ($MonthsLong as $index => $month)
{
    echo '"' . $month . '",';
}
?>"");
var day_names = new Array(
<?php
foreach ($DaysShort as $index => $day)
{
    echo '"' . $day . '",';
}
?>"");
/* ]]> */
</script>
</head>
<body onload="initCalendar();" style="background-color: white;">
<div id="calendar_data"></div>
<div id="clock_data"></div>
</body>
</html>

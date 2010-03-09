<?php // $Id: header.inc.php 128 2009-11-09 13:13:20Z vanpouckesven $
/**
 *	This script displays the Chamilo header.
 *	@package common.html
 */
require_once (dirname(__FILE__) . '/banner.class.php');

// Get language iso-code for this page - ignore errors
// The error ignorance is due to the non compatibility of function_exists()
// with the object syntax of Database::get_language_isocode()

$document_language = AdminDataManager :: get_instance()->retrieve_language_from_english_name($language_interface);
if (empty($document_language))
{
    //if there was no valid iso-code, use the english one
    $document_language = 'en';
}
else
{
	$document_language = $document_language->get_isocode();
}

$header = new Header($document_language);
$header->add_default_headers();
$header->set_page_title(PlatformSetting :: get('institution') . ' - ' . PlatformSetting :: get('site_name'));
if (isset($httpHeadXtra) && $httpHeadXtra)
{
    foreach ($httpHeadXtra as $thisHttpHead)
    {
        $header->add_http_header($thisHttpHead);
    }
}

if (isset($htmlHeadXtra) && $htmlHeadXtra)
{
    foreach ($htmlHeadXtra as $this_html_head)
    {
        $header->add_html_header($this_html_head);
    }
}
$header->display();

if (! isset($text_dir))
{
    $text_dir = 'ltr';
}

echo '<body dir="' . $text_dir . '"';
if (defined('CHAMILO_HOMEPAGE') && CHAMILO_HOMEPAGE)
{
    echo 'onload="javascript:if(document.formLogin) { document.formLogin.login.focus(); }"';
}
echo ">\n";

echo '<!-- #outerframe container to control some general layout of all pages -->' . "\n";
echo '<div id="outerframe">' . "\n";

//  Banner
$banner = new Banner($breadcrumbtrail);
$banner->display();

echo '<div id="main"> <!-- start of #main wrapper for #content and #menu divs -->';
echo '<!--   Begin Of script Output   -->';
echo '<div id="helpbox" class="helpdialog"></div>';
?>
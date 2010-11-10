<?php
namespace common\libraries;

require_once dirname(__FILE__) . '/../../../../../global.inc.php';

echo '<b>Applications</b><br />';
require_once dirname(__FILE__) . '/missing_application_icons.php';
echo '<br /><hr /><br />';

echo '<b>Weblcms Tools</b><br />';
require_once dirname(__FILE__) . '/missing_weblcms_tool_icons.php';
echo '<br /><hr /><br />';

echo '<b>Content Objects</b><br />';
require_once dirname(__FILE__) . '/missing_content_object_icons.php';
echo '<br /><hr /><br />';
?>
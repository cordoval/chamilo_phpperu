<?php
namespace application\weblcms;

use common\libraries\Path;

/**
 * $Id: change_course_module_section.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.ajax
 */
$this_section = 'weblcms';

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';

Utilities :: set_application($this_section);

$course = $_POST['course'];
$target = $_POST['target'];
$source = $_POST['source'];

$targets = split('_', $target);
$target = $targets[1];

$sources = split('_', $source);
$source = $sources[1];

$wdm = WeblcmsDataManager :: get_instance();
$wdm->change_module_course_section($source, $target);

dump($_POST);
?>
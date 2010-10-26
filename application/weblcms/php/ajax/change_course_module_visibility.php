<?php
namespace application\weblcms;

use common\libraries\Path;

/**
 * $Id: change_course_module_visibility.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.ajax
 */
$this_section = 'weblcms';

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';

Utilities :: set_application($this_section);

$module_id = $_POST['tool'];
$visible = $_POST['visible'];

$wdm = WeblcmsDataManager :: get_instance();
$wdm->set_module_id_visible($module_id, $visible);
//dump($visible . ' ' . $module_id);
?>
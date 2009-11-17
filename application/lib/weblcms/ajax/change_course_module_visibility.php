<?php
/**
 * $Id: change_course_module_visibility.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.ajax
 */
$this_section = 'weblcms';

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_manager/weblcms_manager.class.php';
require_once Path :: get_application_path() . 'lib/weblcms/weblcms_data_manager.class.php';

Translation :: set_application($this_section);
Theme :: set_application($this_section);

$module_id = $_POST['tool'];
$visible = $_POST['visible'];

$wdm = WeblcmsDataManager :: get_instance();
$wdm->set_module_id_visible($module_id, $visible);
//dump($visible . ' ' . $module_id);
?>
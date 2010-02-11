<?php
require_once dirname(__FILE__) . '/../../../common/global.inc.php';
require_once dirname(__FILE__).'/gradebook_data_manager.class.php';


$element_id = $_POST['id'];
$value = $_POST['value'];

$fullids = split('_', $element_id);


$ids = explode('|', $fullids[1]);

$user_id = $ids[1];
$gradebook_id = $ids[0];

$dm = GradebookDatamanager::get_instance();
$gradebookreluser = $dm->retrieve_gradebook_rel_user($user_id, $gradebook_id);
$gradebookreluser->set_score($value);

$dm->update_gradebook_rel_user($gradebookreluser);

echo $gradebookreluser->get_score();


?>
<?php

namespace application\package;

require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';

$dm = PackageDataManager :: get_instance();
$languages = $dm->retrieve_package_languages();

while($language = $languages->next_result())
{
	$location = new Location();
    $location->set_location($language->get_english_name());
    $location->set_application(PackageManager :: APPLICATION_NAME);
    $location->set_type('package_language');
    $location->set_identifier($language->get_id());
	$parent = PackageRights :: get_languages_subtree_root();
    $location->set_parent($parent);
    $location->create();
}

?>
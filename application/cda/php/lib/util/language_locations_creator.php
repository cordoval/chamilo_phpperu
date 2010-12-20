<?php
namespace application\cda;

use rights\Location;

require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';

$dm = CdaDataManager :: get_instance();
$languages = $dm->retrieve_cda_languages();

while ($language = $languages->next_result())
{
    $location = new Location();
    $location->set_location($language->get_english_name());
    $location->set_application(CdaManager :: APPLICATION_NAME);
    $location->set_type('cda_language');
    $location->set_identifier($language->get_id());
    $parent = CdaRights :: get_languages_subtree_root();
    $location->set_parent($parent);
    $location->create();
}

?>
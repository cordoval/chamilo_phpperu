<?php
namespace application\internship_organizer;

use common\libraries\OrCondition;
use common\libraries\NotCondition;
use common\libraries\Authentication;
use common\libraries\Path;
use common\libraries\WebApplication;
use common\libraries\CoreApplication;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

use user\User;

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_data_manager.class.php';
require_once Path :: get_common_libraries_class_path() . 'utilities.class.php';
require_once CoreApplication :: get_application_class_lib_path('user') . 'user.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'organisation_rel_user.class.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();

    $organisation_id = $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
    $conditions[] = new EqualityCondition(InternshipOrganizerOrganisationRelUser :: PROPERTY_ORGANISATION_ID, $organisation_id);

    $query_condition = Utilities :: query_to_condition($_GET['query'], array(User :: PROPERTY_FIRSTNAME, User :: PROPERTY_LASTNAME, User :: PROPERTY_USERNAME));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }

    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(User :: PROPERTY_ID, $id);
        }
        $conditions[] = new NotCondition(new OrCondition($c));
    }

    $condition = new AndCondition($conditions);

    $objects = InternshipOrganizerDataManager::get_instance()->retrieve_organisation_rel_users($condition);

    while ($organisation_rel_user = $objects->next_result())
    {
        $organisation_rel_users[] = $organisation_rel_user;
    }

}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($organisation_rel_users);

echo '</tree>';

function dump_tree($organisation_rel_users)
{
    if (contains_results($organisation_rel_users))
    {
        echo '<node id="0" classes="category unlinked" title="', Translation :: get('Users'), '">', "\n";

        foreach ($organisation_rel_users as $organisation_rel_user)
        {
            $id = 'user_' . $organisation_rel_user->get_user_id();
            $name = strip_tags($organisation_rel_user->get_optional_property(User :: PROPERTY_FIRSTNAME) . ' ' . $organisation_rel_user->get_optional_property(User :: PROPERTY_LASTNAME));
            //            $description = strip_tags($period->get_description());
            //            $description = preg_replace("/[\n\r]/", "", $description);


            echo '<leaf id="' . $id . '" classes="" title="' . htmlspecialchars($name) . '" description="' . htmlspecialchars(isset($description) && ! empty($description) ? $description : $name) . '"/>' . "\n";
        }

        echo '</node>' . "\n";

    }
}

function contains_results($objects)
{
    if (count($objects))
    {
        return true;
    }
    return false;
}
?>
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

require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once WebApplication :: get_application_class_lib_path('internship_organizer') . 'internship_organizer_data_manager.class.php';
require_once Path :: get_common_libraries_class_path() . 'utilities.class.php';
require_once CoreApplication :: get_application_class_lib_path('user') . 'user.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'location.class.php';
require_once WebApplication :: get_application_class_path('internship_organizer') . 'internship_organizer_manager/internship_organizer_manager.class.php';

Translation :: set_application(InternshipOrganizerManager :: APPLICATION_NAME);

if (Authentication :: is_valid())
{
    $conditions = array();

    $organisation_id = $_GET[InternshipOrganizerOrganisationManager :: PARAM_ORGANISATION_ID];
    if (isset($organisation_id))
    {
        $conditions[] = new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ORGANISATION_ID, $organisation_id);
    }

    $query_condition = Utilities :: query_to_condition($_GET['query'], array(
            InternshipOrganizerLocation :: PROPERTY_NAME,
            InternshipOrganizerLocation :: PROPERTY_ADDRESS,
            InternshipOrganizerLocation :: PROPERTY_DESCRIPTION,
            InternshipOrganizerLocation :: PROPERTY_EMAIL,
            InternshipOrganizerLocation :: PROPERTY_FAX,
            InternshipOrganizerLocation :: PROPERTY_TELEPHONE));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }

    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(InternshipOrganizerLocation :: PROPERTY_ID, $id);
        }
        $conditions[] = new NotCondition(new OrCondition($c));
    }

    if (count($conditions) > 0)
    {
        $condition = new AndCondition($conditions);
    }
    else
    {
        $condition = null;
    }

    $dm = InternshipOrganizerDataManager :: get_instance();
    $objects = $dm->retrieve_locations($condition);

    while ($location = $objects->next_result())
    {
        $locations[] = $location;
    }
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>', "\n", '<tree>', "\n";

dump_tree($locations);

echo '</tree>';

function dump_tree($locations)
{
    if (contains_results($locations))
    {
        echo '<node id="0" classes="category unlinked" title="', Translation :: get('Locations'), '">', "\n";

        foreach ($locations as $location)
        {
            $id = 'location_' . $location->get_id();
            $name = strip_tags($location->get_name() . ' ' . $location->get_address());
            $description = strip_tags($location->get_description());
            $description = preg_replace("/[\n\r]/", "", $description);
            echo '<leaf id="' . $id . '" classes="" title="' . htmlspecialchars($name) . '" description="' . htmlspecialchars(isset($description) && ! empty($description) ? $description : $name) . '"/>' . "\n";
        }

        echo '</node>', "\n";

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
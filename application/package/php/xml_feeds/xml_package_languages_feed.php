<?php

namespace application\package;

use common\libraries\WebApplication;
use common\libraries\Authentication;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\NotCondition;
use common\libraries\OrCondition;
use common\libraries\AndCondition;
use common\libraries\ObjectTableOrder;
use common\libraries\Translation;
/**
 * $Id: xml_package_languages_feed.php 224 2009-11-13 14:40:30Z kariboe $
 * @package application.lib.package.xml_feeds
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/../../../../../common/global.inc.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_data_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_language.class.php';

if (Authentication :: is_valid())
{
    $conditions = array();

    $query_condition = Utilities :: query_to_condition($_GET['query'], array(PackageLanguage :: PROPERTY_ORIGINAL_NAME, PackageLanguage :: PROPERTY_ENGLISH_NAME));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }

    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(PackageLanguage :: PROPERTY_ID, $id);
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

    $cdm = PackageDataManager :: get_instance();
    $package_languages = $cdm->retrieve_package_languages($condition, null, null, array(new ObjectTableOrder(PackageLanguage :: PROPERTY_ORIGINAL_NAME), new ObjectTableOrder(PackageLanguage :: PROPERTY_ENGLISH_NAME)));
}
else
{
    $package_languages = null;
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<tree>', "\n";

if (isset($package_languages))
{
    dump_tree($package_languages);
}

echo '</tree>';

function dump_tree($package_languages)
{
    if (isset($package_languages) && $package_languages->size() == 0)
    {
        return;
    }

    echo '<node id="0" classes="category unlinked" title="' . Translation :: get('Languages') . '">' . "\n";

    while ($package_language = $package_languages->next_result())
    {
        echo '<leaf id="' . $package_language->get_id() . '" classes="type type_package_language" title="' . htmlspecialchars($package_language->get_original_name()) . '" description="' . htmlspecialchars($package_language->get_english_name()) . '"/>' . "\n";
    }

    echo '</node>' . "\n";
}
?>
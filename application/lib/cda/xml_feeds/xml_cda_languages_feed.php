<?php
/**
 * $Id: xml_cda_languages_feed.php 224 2009-11-13 14:40:30Z kariboe $
 * @package application.lib.cda.xml_feeds
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
require_once dirname(__FILE__) . '/../../../../common/global.inc.php';
require_once Path :: get_application_path() . 'lib/cda/cda_data_manager.class.php';
require_once Path :: get_application_path() . 'lib/cda/cda_language.class.php';

if (Authentication :: is_valid())
{
    $conditions = array();

    $query_condition = Utilities :: query_to_condition($_GET['query'], array(CdaLanguage :: PROPERTY_ORIGINAL_NAME, CdaLanguage :: PROPERTY_ENGLISH_NAME));
    if (isset($query_condition))
    {
        $conditions[] = $query_condition;
    }

    if (is_array($_GET['exclude']))
    {
        $c = array();
        foreach ($_GET['exclude'] as $id)
        {
            $c[] = new EqualityCondition(CdaLanguage :: PROPERTY_ID, $id);
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

    $cdm = CdaDataManager :: get_instance();
    $cda_languages = $cdm->retrieve_cda_languages($condition, null, null, array(new ObjectTableOrder(CdaLanguage :: PROPERTY_ORIGINAL_NAME), new ObjectTableOrder(CdaLanguage :: PROPERTY_ENGLISH_NAME)));
}
else
{
    $cda_languages = null;
}

header('Content-Type: text/xml');
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n" . '<tree>', "\n";

if (isset($cda_languages))
{
    dump_tree($cda_languages);
}

echo '</tree>';

function dump_tree($cda_languages)
{
    if (isset($cda_languages) && $cda_languages->size() == 0)
    {
        return;
    }

    echo '<node id="0" classes="type_category unlinked" title="' . Translation :: get('Languages') . '">' . "\n";

    while ($cda_language = $cda_languages->next_result())
    {
        echo '<leaf id="' . $cda_language->get_id() . '" classes="type type_cda_language" title="' . htmlspecialchars($cda_language->get_original_name()) . '" description="' . htmlspecialchars($cda_language->get_english_name()) . '"/>' . "\n";
    }

    echo '</node>' . "\n";
}
?>
<?php
namespace application\phrases;

use common\libraries\ObjectTableDataProvider;
use common\libraries\ObjectTableOrder;
/**
 * $Id: phrases_mastery_level_browser_table_data_provider.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.assessment.assessment_manager.component.assessment_mastery_level_browser
 */
/**
 * Data provider for a assessment_mastery_level table
 *
 * @author Hans De Bisschop
 * @author
 */
class PhrasesMasteryLevelBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param ApplicationComponent $browser
     * @param Condition $condition
     */
    function PhrasesMasteryLevelBrowserTableDataProvider($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    /**
     * Retrieves the objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of objects
     */
    function get_objects($offset, $count, $order_property = null)
    {
        //$order_property = $this->get_order_property($order_property);
        $order_property = new ObjectTableOrder(PhrasesMasteryLevel :: PROPERTY_DISPLAY_ORDER);

        return $this->get_browser()->retrieve_phrases_mastery_levels($this->get_condition(), $offset, $count, $order_property);
    }

    /**
     * Gets the number of objects in the table
     * @return int
     */
    function get_object_count()
    {
        return $this->get_browser()->count_phrases_mastery_levels($this->get_condition());
    }
}
?>
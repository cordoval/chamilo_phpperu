<?php

namespace application\handbook;

use common\libraries\ObjectTableDataProvider;
use common\libraries\Request;
use application\context_linker\ContextLinkerManager;
use repository\RepositoryDataManager;
use common\libraries\ArrayResultSet;
use repository\ContentObject;
use application\metadata\MetadataPropertyType;
use application\metadata\MetadataPropertyValue;

/**
 * Data provider for a user browser table.
 *
 * This class implements some functions to allow user browser tables to
 * retrieve information about the users to display.
 */
class HandbookAlternativesPickerItemTableDataProvider extends ObjectTableDataProvider {

    /**
     * Constructor
     * @param HandbookPublicationManagerComponent $browser
     * @param Condition $condition
     */
    function __construct($browser, $condition) {
        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the users
     * @param String $user
     * @param String $category
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null) {
        $order_property = $this->get_order_property($order_property);

        //get all alternatives for this item
        $rdm = RepositoryDataManager::get_instance();
        $item_id = Request :: get(HandbookManager::PARAM_HANDBOOK_SELECTION_ID);
        $selected_object = $rdm->retrieve_content_object($item_id);
        $co_id = $selected_object->get_reference();
        $handbook_id = Request::get(HandbookManager::PARAM_HANDBOOK_ID);
        $alternatives_array = HandbookManager::get_alternative_items($co_id);


        //add original to array
        //TODO: get actual data
        $original['alt_' . ContentObject :: PROPERTY_TITLE] = 'orig';
        $original['orig_' . ContentObject :: PROPERTY_TITLE] = 'orig';
       $original['alt_' . ContentObject :: PROPERTY_TYPE] = 'orig';
        $original[MetadataPropertyType :: PROPERTY_NS_PREFIX] = 'orig';
        $original[MetadataPropertyType :: PROPERTY_NAME] = 'orig';
        $original[MetadataPropertyValue :: PROPERTY_VALUE] = 'orig';
        $original['alt_id'] = $co_id;
        $alternatives_array[] = $original;


        if ($alternatives_array != false && (count($alternatives_array) > 0))
        {
            return new ArrayResultSet($alternatives_array);
        }
        else
        {
            return null;
        }


    }

    /**
     * Gets the number of handbooks in the table
     * @return int
     */
    function get_object_count()
    {
//        return 1;
//        $condition = $this->get_condition();
        return HandbookManager::get_alternative_items($item_id);
    }

}

?>
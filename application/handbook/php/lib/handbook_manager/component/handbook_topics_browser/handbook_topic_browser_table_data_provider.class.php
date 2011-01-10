<?php
namespace application\handbook;
use common\libraries\ObjectTableDataProvider;
use repository\RepositoryDataManager;
use repository\content_object\handbook_topic\HandbookTopic;


/**
 * Data provider for a user browser table.
 *
 * This class implements some functions to allow user browser tables to
 * retrieve information about the users to display.
 */
class HandbookTopicBrowserTableDataProvider extends ObjectTableDataProvider
{

    /**
     * Constructor
     * @param HandbookTopicManagerComponent $browser
     * @param Condition $condition
     */
    function __construct($browser, $condition)
    {
           
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
    function get_objects($offset, $count, $order_property = null)
    {

        $order_property = $this->get_order_property($order_property);
        return RepositoryDataManager :: get_instance()->retrieve_type_content_objects(HandbookTopic::get_type_name(), $this->get_condition());
    }

    /**
     * Gets the number of handbooks in the table
     * @return int
     */
    function get_object_count()
    {
        $condition = $this->get_condition();
        return RepositoryDataManager :: get_instance()->retrieve_type_content_objects(HandbookTopic::get_type_name(), $this->get_condition());
    }
}
?>
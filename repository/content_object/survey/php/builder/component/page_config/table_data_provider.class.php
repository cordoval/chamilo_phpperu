<?php
namespace repository\content_object\survey;

use common\libraries\ObjectTableDataProvider;
use repository\RepositoryDataManager;

class SurveyPageConfigTableDataProvider 
{

    /**
     * Constructor
     * @param RepositoryManagerComponent $browser
     * @param Condition $condition
     */
    function __construct($browser, $condition)
    {
//        parent :: __construct($browser, $condition);
    }

    /**
     * Gets the learning objects
     * @param int $offset
     * @param int $count
     * @param string $order_property
     * @return ResultSet A set of matching learning objects.
     */
    function get_objects($offset, $count, $order_property = null)
    {
    	$page = RepositoryDataManager::get_instance()->retrieve_content_object($this->get_condition());
    	dump($page->get_config());
    	return $page->get_config();
    }

    /**
     * Gets the number of learning objects in the table
     * @return int
     */
//    function get_object_count()
//    {
//      	$page = RepositoryDataManager::get_instance()->retrieve_content_object($this->get_condition());
//    	dump(count($page->get_config()));
//      	return count($page->get_config());
//    }
}
?>
<?php
namespace application\handbook;
use common\libraries\Database;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\SubselectCondition;
use repository\ContentObject;
use repository\RepositoryDataManager;
use repository\content_object\handbook_item\HandbookItem;
use repository\DatabaseContentObjectResultSet;

/**
 * @package handbook.datamanager
 */
require_once dirname(__FILE__).'/../handbook_publication.class.php';
require_once dirname(__FILE__).'/../handbook_data_manager.interface.class.php';
require_once dirname(__FILE__).'/../../../../../repository/php/lib/data_manager/database/database_content_object_result_set.class.php';


/**
 *	This is a data manager that uses a database for storage. It was written
 *	for MySQL, but should be compatible with most SQL flavors.
 *
 *      @author Nathalie Blocry
 */

class DatabaseHandbookDataManager extends Database implements HandbookDataManagerInterface
{
	function initialize()
	{
         parent :: initialize();
		$this->set_prefix('handbook_');
	}





	function get_next_handbook_publication_id()
	{
		return $this->get_next_id(HandbookPublication :: get_table_name());
	}

	function create_handbook_publication($handbook_publication)
	{
		return $this->create($handbook_publication);
	}

	function update_handbook_publication($handbook_publication)
	{
		$condition = new EqualityCondition(HandbookPublication :: PROPERTY_ID, $handbook_publication->get_id());
		return $this->update($handbook_publication, $condition);
	}

	function delete_handbook_publication($handbook_publication)
	{
		$condition = new EqualityCondition(HandbookPublication :: PROPERTY_ID, $handbook_publication->get_id());
		return $this->delete($handbook_publication->get_table_name(), $condition);
	}

//        function count_handbooks($condition = null)
//	{
//		return $this->count_objects(HandbookPublication :: get_table_name(), $condition);
//	}


	function retrieve_handbooks($search_condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
                $conditons = array();
                $conditions[] = $search_condition;
                $conditions[] = new EqualityCondition(ContentObject::PROPERTY_TYPE, 'handbook');
                $condition = new AndCondition($conditons);

		return $this->retrieve_objects(ContentObject:: get_table_name(), $condition, $offset, $max_objects, $order_by, ContentObject::CLASS_NAME);
	}

        function retrieve_published_handbooks($search_condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
                $conditons = array();
                $conditions[] = $search_condition;
//                $conditions[] = new EqualityCondition(ContentObject::PROPERTY_TYPE, 'handbook');
                 $conditions[] = new SubselectCondition(ContentObject::PROPERTY_ID, HandbookPublication::PROPERTY_CONTENT_OBJECT_ID, HandbookPublication::get_table_name(), null, null, HandbookDataManager::get_instance());

                $condition = new AndCondition($conditions);

		return RepositoryDataManager::get_instance()->retrieve_objects(ContentObject:: get_table_name(), $condition, $offset, $max_objects, $order_by, ContentObject::CLASS_NAME);
	}




	function count_handbook_publications($search_condition = null)
	{
            $test= $search_condition->get_conditions();
            if($test)
            {
                $conditons = array();
                $conditions[] = $search_condition;
                $conditions[] = new SubselectCondition(ContentObject::PROPERTY_ID, HandbookPublication::PROPERTY_CONTENT_OBJECT_ID, HandbookPublication::get_table_name(), null, null, HandbookDataManager::get_instance());

                $condition = new AndCondition($conditions);
            }
            else
            {
                $search_condition = null;
            }
		return $this->count_objects(HandbookPublication :: get_table_name(), $condition);
	}


	function retrieve_handbook_publication($id)
	{
		$condition = new EqualityCondition(HandbookPublication :: PROPERTY_ID, $id);
		return $this->retrieve_object(HandbookPublication :: get_table_name(), $condition, null, HandbookPublication::CLASS_NAME);
	}

	function retrieve_handbook_publications($condition = null, $offset = null, $max_objects = null, $order_by = null)
	{
		return $this->retrieve_objects(HandbookPublication :: get_table_name(), $condition, $offset, $max_objects, $order_by, HandbookPublication::CLASS_NAME);
	}

            public function any_content_object_is_published($object_ids) {
            }
            public function content_object_is_published($object_id) {
            }
            public function count_handbook_publication_groups($conditions = null) {
            }
            public function count_handbook_publication_users($conditions = null) {
            }
            public function count_publication_attributes($user = null, $object_id = null, $condition = null) {
            }
            public function create_handbook_information($handbook_publication) {
            }
            public function create_handbook_publication_group($handbook_publication_group) {
            }
            public function create_handbook_publication_user($handbook_publication_user) {
            }
            public function delete_content_object_publications($object_id) {
            }
            public function delete_handbook_publication_group($handbook_publication_group) {
            }
            public function delete_handbook_publication_user($handbook_publication_user) {
            }
            public function get_content_object_publication_attribute($publication_id) {
            }
            public function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null) {
            }
            public function get_handbook_children($handbook_id) {
            }
            public function retrieve_handbook_information_by_user($user_id) {
            }
            public function retrieve_handbook_publication_groups($condition = null, $offset = null, $count = null, $order_property = null) {
            }
            public function retrieve_handbook_publication_users($condition = null, $offset = null, $count = null, $order_property = null) {
            }
            public function update_content_object_publication_id($publication_attr) {
            }
            public function update_handbook_information($handbook_information) {
            }

            /**
             *
             * @param <type> $uuid
             * @return array with handbook_item_id and reference_id
             */
    function retrieve_handbook_item_data_by_uuid($uuid)
    {
        $rdm = RepositoryDataManager::get_instance();
        $content_object_alias = 'co' ;
        $query = 'SELECT * FROM ' . $rdm ->escape_table_name(ContentObject::get_table_name()) . ' AS ' . $content_object_alias;

            $type_alias  ='hi';
            $query .= ' JOIN ' . $rdm ->escape_table_name(HandbookItem::get_type_name()) . ' AS ' . $type_alias . ' ON ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $content_object_alias) . ' = ' . $this->escape_column_name(ContentObject :: PROPERTY_ID, $type_alias);
      
            $query .= ' WHERE ' .$type_alias .'.'. HandbookItem::PROPERTY_UUID. ' = \'' . $uuid . '\'';

        $res = $this->query($query);
        $record = $res->fetchRow(\MDB2_FETCHMODE_ASSOC);
      $handbook_item[HandbookItem :: PROPERTY_ID] =  $record[HandbookItem :: PROPERTY_ID];
       $handbook_item[HandbookItem :: PROPERTY_UUID] = $record[HandbookItem :: PROPERTY_UUID];
        $handbook_item[HandbookItem :: PROPERTY_REFERENCE] = $record[HandbookItem :: PROPERTY_REFERENCE];                                  
                                                     
        var_dump($handbook_item);
        $res->free();
        return $handbook_item;
    }



}
?>
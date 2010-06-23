<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of database_mediamosa_streaming_media_data_managerclass
 *
 * @author jevdheyd
 */
class DatabaseMediamosaStreamingMediaDataManager extends Database
{


	function initialize()
	{
         parent :: initialize();
		$this->set_prefix('mediamosa_');
	}

        function retrieve_streaming_media_server_objects($condition = null, $order_by = null, $offset = null, $max_objects = null)
        {
            return $this->retrieve_objects(StreamingMediaServerObject :: get_table_name(), $condition, $offset, $max_objects, $order_by);
        }

        function retrieve_streaming_media_server_object($id)
        {
            $condition = new EqualityCondition(StreamingMediaServerObject :: PROPERTY_ID, $id);
            return $this->retrieve_object(StreamingMediaServerObject :: get_table_name(), $condition);
        }

        function count_streaming_media_server_objects($condition = null)
        {
            return $this->count_objects(StreamingMediaServerObject :: get_table_name(), $condition);
        }

        function create_streaming_media_server_object($mediamosa_server_object)
        {
           
            return $this->create($mediamosa_server_object);
        }

        function update_streaming_media_server_object($mediamosa_server_object)
        {
            $condition = new EqualityCondition(StreamingMediaServerObject :: PROPERTY_ID, $mediamosa_server_object->get_id());
            return $this->update($mediamosa_server_object, $condition);
        }

        function delete_streaming_media_server_object($mediamosa_server_object)
        {
            $condition = new EqualityCondition(StreamingMediaServerObject :: PROPERTY_ID, $mediamosa_server_object->get_id());
            
            return $this->delete(StreamingMediaServerObject :: get_table_name(), $condition);
        }

        function create_streaming_media_server_object_table()
        {
            $query = 'CREATE TABLE IF NOT EXISTS ' . $this->get_prefix() . StreamingMediaServerObject :: get_table_name() . '
                      (
                            ' .StreamingMediaServerObject :: PROPERTY_ID. ' INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                            ' .StreamingMediaServerObject :: PROPERTY_TITLE. ' VARCHAR (255) NOT NULL,
                            '. StreamingMediaServerObject :: PROPERTY_URL .' VARCHAR (255) NOT NULL,
                            ' .StreamingMediaServerObject :: PROPERTY_LOGIN. ' VARCHAR (255) NOT NULL,
                            ' .StreamingMediaServerObject :: PROPERTY_PASSWORD. ' VARCHAR (255) NOT NULL,
                            is_upload_possible TINYINT(10) UNSIGNED
                        )';
            
            $this->query($query);
        }
}
?>

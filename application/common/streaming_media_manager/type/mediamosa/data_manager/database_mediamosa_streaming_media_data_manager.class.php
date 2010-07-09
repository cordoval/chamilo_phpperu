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

        function retrieve_streaming_media_user_quotum($user_id, $server_id)
        {
            $condition1 = new EqualityCondition(StreamingMediaUserQuotum :: PROPERTY_USER_ID, $user_id);
            $condition2 = new EqualityCondition(StreamingMediaUserQuotum :: PROPERTY_SERVER_ID, $server_id);

            $condition = new AndCondition($condition1, $condition2);

            return $this->retrieve_object(StreamingMediaUserQuotum :: get_table_name(), $condition);
        }

        function retrieve_streaming_media_user_quota($user_id)
        {
            
            $condition = new EqualityCondition(StreamingMediaUserQuotum :: PROPERTY_USER_ID, $user_id);

            return $this->retrieve_objects(StreamingMediaUserQuotum :: get_table_name(), $condition);
        }

        function count_streaming_media_server_objects($condition = null)
        {
            return $this->count_objects(StreamingMediaServerObject :: get_table_name(), $condition);
        }

        function create_streaming_media_server_object($mediamosa_server_object)
        {
           
            return $this->create($mediamosa_server_object);
        }

        function create_streaming_media_user_quotum($mediamosa_user_quotum)
        {
            return $this->create($mediamosa_user_quotum);
        }

        function update_streaming_media_server_object($mediamosa_server_object)
        {
            $condition = new EqualityCondition(StreamingMediaServerObject :: PROPERTY_ID, $mediamosa_server_object->get_id());
            return $this->update($mediamosa_server_object, $condition);
        }

        function update_streaming_media_user_quotum($mediamosa_user_quotum)
        {
            $condition = new EqualityCondition(StreamingMediaUserQuotum :: PROPERTY_ID, $mediamosa_user_quotum->get_id());
            return $this->update($mediamosa_user_quotum, $condition);
        }

        function delete_streaming_media_server_object($mediamosa_server_object)
        {
            $server_id = $mediamosa_server_object->get_id();
            $condition = new EqualityCondition(StreamingMediaServerObject :: PROPERTY_ID, $server_id);
            
            if($this->delete(StreamingMediaServerObject :: get_table_name(), $condition))
            {
                return $this->delete_streaming_media_user_quota($server_id);
            }
        }

        function delete_streaming_media_user_quotum($mediamosa_user_quotum)
        {
            $condition1 = new EqualityCondition(StreamingMediaUserQuotum :: PROPERTY_SERVER_ID, $mediamosa_user_quotum->get_server_id());
            $condition2 = new EqualityCondition(StreamingMediaUserQuotum :: PROPERTY_USER_ID, $mediamosa_user_quotum->get_user_id());

            $condition = new AndCondition($condition1, $condition2);
             
            return $this->delete(StreamingMediaUserQuotum :: get_table_name(), $condition);
        }

        //deletes all quota for a server
        function delete_streaming_media_user_quota($server_id)
        {
            $condition = new EqualityCondition(StreamingMediaUserQuotum :: PROPERTY_SERVER_ID, $server_id);
            return $this->delete(StreamingMediaUserQuotum :: get_table_name(), $condition);
        }

        function create_streaming_media_server_object_table()
        {
           
            $query = 'CREATE TABLE IF NOT EXISTS ' . $this->get_prefix() . StreamingMediaServerObject :: get_table_name() . '
                      (
                            ' . StreamingMediaServerObject :: PROPERTY_ID . ' INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                            ' . StreamingMediaServerObject :: PROPERTY_TITLE . ' VARCHAR (255) NOT NULL,
                            ' . StreamingMediaServerObject :: PROPERTY_URL . ' VARCHAR (255) NOT NULL,
                            ' . StreamingMediaServerObject :: PROPERTY_LOGIN. ' VARCHAR (255) NOT NULL,
                            ' . StreamingMediaServerObject :: PROPERTY_PASSWORD . ' VARCHAR (255) NOT NULL,
                            ' . StreamingMediaServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE . ' TINYINT(10) UNSIGNED,
                            ' . StreamingMediaServerObject :: PROPERTY_IS_DEFAULT . ' TINYINT(1) UNSIGNED,
                            ' . StreamingMediaServerObject :: PROPERTY_VERSION . ' VARCHAR(255) NOT NULL,
                            ' . StreamingMediaServerObject :: PROPERTY_DEFAULT_USER_QUOTUM . ' INT NOT NULL,
                           UNIQUE(' . StreamingMediaServerObject :: PROPERTY_IS_DEFAULT . ')
                        )';
            
            $this->query($query);

            $query =  'CREATE TABLE IF NOT EXISTS ' . $this->get_prefix() . StreamingMediaUserQuotum :: get_table_name() . '
                      (
                            ' .StreamingMediaUserQuotum :: PROPERTY_ID. ' INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                            ' .StreamingMediaUserQuotum :: PROPERTY_USER_ID. ' INT NOT NULL,
                            ' .StreamingMediaUserQuotum :: PROPERTY_SERVER_ID. ' INT NOT NULL,
                            '. StreamingMediaUserQuotum :: PROPERTY_QUOTUM .' INT NOT NULL,
                                INDEX(' .StreamingMediaUserQuotum :: PROPERTY_USER_ID. '),
                               INDEX(' .StreamingMediaUserQuotum :: PROPERTY_SERVER_ID. ')
                        )';
            $this->query($query);
        }
}
?>

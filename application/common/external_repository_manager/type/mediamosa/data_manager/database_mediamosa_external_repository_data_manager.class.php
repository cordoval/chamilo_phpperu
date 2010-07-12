<?php

require_once dirname(__FILE__) . '/../mediamosa_external_repository_data_manager.interface.class.php';

class DatabaseMediamosaExternalRepositoryDataManager extends Database implements MediamosaExternalRepositoryDataManagerInterface
{
    function initialize()
    {
     parent :: initialize();
            $this->set_prefix('mediamosa_');
    }

    function retrieve_external_repository_server_objects($condition = null, $order_by = null, $offset = null, $max_objects = null)
    {
        return $this->retrieve_objects(ExternalRepositoryServerObject :: get_table_name(), $condition, $offset, $max_objects, $order_by);
    }

    function retrieve_external_repository_server_object($id)
    {
        $condition = new EqualityCondition(ExternalRepositoryServerObject :: PROPERTY_ID, $id);
        return $this->retrieve_object(ExternalRepositoryServerObject :: get_table_name(), $condition);
    }

    function retrieve_external_repository_user_quotum($user_id, $server_id)
    {
        $condition1 = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_USER_ID, $user_id);
        $condition2 = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_SERVER_ID, $server_id);

        $condition = new AndCondition($condition1, $condition2);

        return $this->retrieve_object(ExternalRepositoryUserQuotum :: get_table_name(), $condition);
    }

    function retrieve_external_repository_user_quota($user_id)
    {

        $condition = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_USER_ID, $user_id);

        return $this->retrieve_objects(ExternalRepositoryUserQuotum :: get_table_name(), $condition);
    }

    function count_external_repository_server_objects($condition = null)
    {
        return $this->count_objects(ExternalRepositoryServerObject :: get_table_name(), $condition);
    }

    function create_external_repository_server_object($mediamosa_server_object)
    {

        return $this->create($mediamosa_server_object);
    }

    function create_external_repository_user_quotum($mediamosa_user_quotum)
    {
        return $this->create($mediamosa_user_quotum);
    }

    function update_external_repository_server_object($mediamosa_server_object)
    {
        $condition = new EqualityCondition(ExternalRepositoryServerObject :: PROPERTY_ID, $mediamosa_server_object->get_id());
        return $this->update($mediamosa_server_object, $condition);
    }

    function update_external_repository_user_quotum($mediamosa_user_quotum)
    {
        $condition = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_ID, $mediamosa_user_quotum->get_id());
        return $this->update($mediamosa_user_quotum, $condition);
    }

    function delete_external_repository_server_object($mediamosa_server_object)
    {
        $server_id = $mediamosa_server_object->get_id();
        $condition = new EqualityCondition(ExternalRepositoryServerObject :: PROPERTY_ID, $server_id);

        if($this->delete(ExternalRepositoryServerObject :: get_table_name(), $condition))
        {
            return $this->delete_external_repository_user_quota($server_id);
        }
    }

    function delete_external_repository_user_quotum($mediamosa_user_quotum)
    {
        $condition1 = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_SERVER_ID, $mediamosa_user_quotum->get_server_id());
        $condition2 = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_USER_ID, $mediamosa_user_quotum->get_user_id());

        $condition = new AndCondition($condition1, $condition2);

        return $this->delete(ExternalRepositoryUserQuotum :: get_table_name(), $condition);
    }

    //deletes all quota for a server
    function delete_external_repository_user_quota($server_id)
    {
        $condition = new EqualityCondition(ExternalRepositoryUserQuotum :: PROPERTY_SERVER_ID, $server_id);
        return $this->delete(ExternalRepositoryUserQuotum :: get_table_name(), $condition);
    }

    function create_external_repository_server_object_table()
    {

        $query = 'CREATE TABLE IF NOT EXISTS ' . $this->get_prefix() . ExternalRepositoryServerObject :: get_table_name() . '
                  (
                        ' . ExternalRepositoryServerObject :: PROPERTY_ID . ' INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                        ' . ExternalRepositoryServerObject :: PROPERTY_TITLE . ' VARCHAR (255) NOT NULL,
                        ' . ExternalRepositoryServerObject :: PROPERTY_URL . ' VARCHAR (255) NOT NULL,
                        ' . ExternalRepositoryServerObject :: PROPERTY_LOGIN. ' VARCHAR (255) NOT NULL,
                        ' . ExternalRepositoryServerObject :: PROPERTY_PASSWORD . ' VARCHAR (255) NOT NULL,
                        ' . ExternalRepositoryServerObject :: PROPERTY_IS_UPLOAD_POSSIBLE . ' TINYINT(10) UNSIGNED,
                        ' . ExternalRepositoryServerObject :: PROPERTY_IS_DEFAULT . ' TINYINT(1) UNSIGNED,
                        ' . ExternalRepositoryServerObject :: PROPERTY_VERSION . ' VARCHAR(255) NOT NULL,
                        ' . ExternalRepositoryServerObject :: PROPERTY_DEFAULT_USER_QUOTUM . ' INT NOT NULL,
                       UNIQUE(' . ExternalRepositoryServerObject :: PROPERTY_IS_DEFAULT . ')
                    )';

        $this->query($query);

        $query =  'CREATE TABLE IF NOT EXISTS ' . $this->get_prefix() . ExternalRepositoryUserQuotum :: get_table_name() . '
                  (
                        ' .ExternalRepositoryUserQuotum :: PROPERTY_ID. ' INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
                        ' .ExternalRepositoryUserQuotum :: PROPERTY_USER_ID. ' INT NOT NULL,
                        ' .ExternalRepositoryUserQuotum :: PROPERTY_SERVER_ID. ' INT NOT NULL,
                        '. ExternalRepositoryUserQuotum :: PROPERTY_QUOTUM .' INT NOT NULL,
                            INDEX(' .ExternalRepositoryUserQuotum :: PROPERTY_USER_ID. '),
                           INDEX(' .ExternalRepositoryUserQuotum :: PROPERTY_SERVER_ID. ')
                    )';
        $this->query($query);
    }
}
?>

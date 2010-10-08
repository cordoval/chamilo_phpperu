<?php
require_once dirname(__FILE__) . '/../cas_password.class.php';
require_once Path :: get_library_path() . 'webservice/webservice.class.php';

class MysqlCasPassword extends CasPassword
{
    /**
     * Nothing is done with the password by default
     * This just prevents errors.
     *
     * @param String $old_password The user's old password
     * @param String $new_password The user's new password
     */
    function set_password($old_password, $new_password)
    {
        $configuration = array();
        $configuration['user'] = 'user';
        $configuration['password'] = 'password';
        $configuration['server'] = 'server';
        $configuration['port'] = '3306';
        $configuration['database'] = 'database';
        $configuration['table'] = 'table';
        $configuration['column_id'] = 'username';
        $configuration['column_password'] = 'password';
        $configuration['hashing'] = 'md5';

    	$connection = MDB2 :: connect('mysqli://'.$configuration['user'].':'.$configuration['password'].'@'.$configuration['server'].':'.$configuration['port'].'/'.$configuration['database'], array('debug' => 3));
    	$connection->setCharset('utf8');

    	// Retrieve the user AND check whether the passwords match
    	$query  = 'SELECT * FROM ' . $configuration['database'] . '.' . $configuration['table'];
    	$query .= ' WHERE ' . $configuration['column_id'] . ' = ' . $connection->quote($this->get_user()->get_username());
    	$query .= ' AND ' . $configuration['column_password'] . ' = ' . $connection->quote(hash($configuration['hashing'], $old_password));

    	$connection->setLimit(1);
    	$result = $connection->query($query);
        $record = $result->fetchRow(MDB2_FETCHMODE_ASSOC);
        $result->free();

        if (!$record)
        {
            return false;
        }

        $props = array();
        $props['password'] = hash($configuration['hashing'], $new_password);

        $connection->loadModule('Extended');
        $condition = new EqualityCondition($configuration['column_id'], $this->get_user()->get_username());
        $result = $connection->extended->autoExecute($configuration['table'], $props, MDB2_AUTOQUERY_UPDATE, $condition);

        if (MDB2 :: isError($result))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    function is_password_changeable()
    {
        return true;
    }

    function get_password_requirements()
    {
        return Translation :: get('GeneralPasswordRequirements');
    }
}
?>
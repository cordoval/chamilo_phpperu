<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of mediamosa_streaming_media_server_objectclass
 *
 * @author jevdheyd
 */
class StreamingMediaServerObject extends DataClass{

    //id, url, name, login, password, is_upload_possible

    const CLASS_NAME = 'StreamingMediaServerObject';
    const PREFIX = 'mediamosa_';
    //const PROPERTY_ID = 'id';
    const PROPERTY_TITLE = 'title';
    const PROPERTY_URL = 'url';
    const PROPERTY_LOGIN = 'loginname';
    const PROPERTY_PASSWORD = 'password';
    const PROPERTY_IS_UPLOAD_POSSIBLE = 'is_upload_possible';

/*
    function set_id($id)
    {
        $this->set_default_property(self :: PROPERTY_ID, $id);
    }

    function get_id()
    {
        return $this->get_default_property(self :: PROPERTY_ID);
    }
*/
    /**
     * Get the default properties of all server_objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_TITLE, self :: PROPERTY_LOGIN, self :: PROPERTY_PASSWORD, self :: PROPERTY_IS_UPLOAD_POSSIBLE, self :: PROPERTY_URL));
    }

    function set_title($title)
    {
        $this->set_default_property(self :: PROPERTY_TITLE, $title);
    }

    function get_title()
    {
        return $this->get_default_property(self :: PROPERTY_TITLE);
    }

    function set_url($url)
    {
        $this->set_default_property(self :: PROPERTY_URL, $url);
    }

    function get_url()
    {
        return $this->get_default_property(self :: PROPERTY_URL);
    }

    function set_login($login)
    {
        $this->set_default_property(self :: PROPERTY_LOGIN, $login);
    }

    function get_login()
    {
        return $this->get_default_property(self :: PROPERTY_LOGIN);
    }

    function set_password($password)
    {
        $this->set_default_property(self :: PROPERTY_PASSWORD, $password);
    }

    function get_password()
    {
        return $this->get_default_property(self :: PROPERTY_PASSWORD);
    }
    
    function set_is_upload_possible($is_upload_possible)
    {
        $this->set_default_property(self :: PROPERTY_IS_UPLOAD_POSSIBLE, $is_upload_possible);
    }
    
    function get_is_upload_possible()
    {
        return $this->get_default_property(self :: PROPERTY_IS_UPLOAD_POSSIBLE);
    }

    function get_data_manager()
    {
        return MediamosaStreamingMediaDataManager :: get_instance();
    }

    static function get_table_name()
    {
        return  Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }
}
?>

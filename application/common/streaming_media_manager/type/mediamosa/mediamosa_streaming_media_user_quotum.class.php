<?php
/**
 * Description of mediamosa_streaming_media_user_quotumclass
 *
 * @author jevdheyd
 */
class StreamingMediaUserQuotum extends DataClass {

    const CLASS_NAME = 'StreamingMediaUserQuotum';
    const PREFIX = 'mediamosa_';
    const PROPERTY_USER_ID = 'user_id';
    const PROPERTY_SERVER_ID = 'server_id';
    const PROPERTY_QUOTUM = 'quotum';

    /**
     * Get the default properties of all server_objects.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_SERVER_ID, self :: PROPERTY_USER_ID, self :: PROPERTY_QUOTUM));
    }

    function set_user_id($user_id)
    {
        $this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
    }

    function get_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_USER_ID);
    }

    function set_server_id($server_id)
    {
        $this->set_default_property(self :: PROPERTY_SERVER_ID, $server_id);
    }

    function get_server_id()
    {
        return $this->get_default_property(self :: PROPERTY_SERVER_ID);
    }

    function set_quotum($quotum)
    {
        $this->set_default_property(self :: PROPERTY_QUOTUM, $quotum);
    }

    function get_quotum()
    {
        return $this->get_default_property(self :: PROPERTY_QUOTUM);
    }

    static function get_table_name()
    {
        return  Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    function get_data_manager(){
        return MediamosaStreamingMediaDataManager :: get_instance();
    }
}
?>

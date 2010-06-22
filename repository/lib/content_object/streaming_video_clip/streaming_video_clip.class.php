<?php
/*
 * @author jevdheyd
 */
class StreamingVideoClip extends ContentObject implements Versionable
{
    const PROPERTY_SERVER_ID = 'server_id';
    const PROPERTY_ASSET_ID = 'asset_id';

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_SERVER_ID, self :: PROPERTY_ASSET_ID);
    }

    function set_server_id($server_id)
    {
        $this->set_additional_property(self :: PROPERTY_SERVER_ID, $server_id);
    }

    function get_server_id()
    {
        return $this->get_additional_property(self :: PROPERTY_SERVER_ID);
    }

    function set_asset_id($asset_id)
    {
        $this->set_additional_property(self :: PROPERTY_ASSET_ID, $asset_id);
    }

    function get_asset_id()
    {
        return $this->get_additional_property(self :: PROPERTY_ASSET_ID);
    }

    //Inherited
    function supports_attachments()
    {
        return false;
    }

}
?>
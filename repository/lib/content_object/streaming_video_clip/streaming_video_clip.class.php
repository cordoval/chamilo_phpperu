<?php
/*
 * @author jevdheyd
 */
class StreamingVideoClip extends ContentObject implements Versionable
{
    
    const PROPERTY_PUBLISHER = 'publisher';
    const PROPERTY_CREATOR = 'creator';
    const PROPERTY_THUMBNAIL_URL = 'thumbnail_url';

    const CLASS_NAME = __CLASS__;

    static function get_type_name()
    {
        return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_CREATOR, self :: PROPERTY_PUBLISHER, self :: PROPERTY_THUMBNAIL_URL);
    }



     function set_publisher($publisher)
    {
        $this->set_additional_property(self :: PROPERTY_PUBLISHER, $publisher);
    }

    function get_publisher()
    {
        return $this->get_additional_property(self::PROPERTY_PUBLISHER);
    }

    function set_creator($creator)
    {
        $this->set_additional_property(self :: PROPERTY_CREATOR, $creator);
    }

    function get_creator()
    {
        return $this->get_additional_property(self :: PROPERTY_CREATOR);
    }

    function set_thumbnail_url($thumbnail)
    {
        $this->set_additional_property(self :: PROPERTY_THUMBNAIL_URL, $thumbnail);
    }

    function get_thumbnail_url()
    {
        return $this->get_additional_property(self :: PROPERTY_THUMBNAIL_URL);
    }
    
//    /**
//     * @return ExternalRepository
//     */
//    function get_external_repository()
//    {
//        return RepositoryDataManager :: get_instance()->retrieve_external_repository($this->get_server_id());
//    }

   
}
?>
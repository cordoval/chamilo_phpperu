<?php
class PhotoGallery extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'photo_gallery';
    
    /**
     * PhotoGallery properties
     */
    const PROPERTY_CONTENT_OBJECT = 'content_object_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CONTENT_OBJECT));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PhotoGalleryDataManager :: get_instance();
    }

    /**
     * Returns the content_object of this PhotoGallery.
     * @return the content_object.
     */
    function get_content_object()
    {
        return $this->get_default_property(self :: PROPERTY_CONTENT_OBJECT);
    }

    /**
     * Sets the content_object of this PhotoGallery.
     * @param content_object
     */
    function set_content_object($content_object)
    {
        $this->set_default_property(self :: PROPERTY_CONTENT_OBJECT, $content_object);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

    function get_photo_gallery_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_content_object());
    }

//    function get_publication_publisher()
//    {
//        $udm = UserDataManager :: get_instance();
//        return $udm->retrieve_user($this->get_publisher());
//    }
}
?>
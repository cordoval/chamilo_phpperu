<?php
require_once dirname(__FILE__) . '/../../../../table/default_external_repository_gallery_object_table_property_model.class.php';

class PicasaExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function PicasaExternalRepositoryGalleryTablePropertyModel()
    {
        parent :: __construct();

        $picasa_properties = PicasaExternalRepositoryConnector :: get_sort_properties();

        foreach (PicasaExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>
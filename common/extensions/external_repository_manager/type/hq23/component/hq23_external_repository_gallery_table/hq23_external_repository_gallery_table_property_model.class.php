<?php
require_once dirname(__FILE__) . '/../../../../table/default_external_repository_gallery_object_table_property_model.class.php';

class Hq23ExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function Hq23ExternalRepositoryGalleryTablePropertyModel()
    {
        parent :: __construct();

        $hq23_properties = Hq23ExternalRepositoryConnector :: get_sort_properties();

        foreach (Hq23ExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>
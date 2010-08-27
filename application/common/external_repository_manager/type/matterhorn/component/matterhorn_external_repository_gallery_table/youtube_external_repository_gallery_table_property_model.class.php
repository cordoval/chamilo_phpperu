<?php
require_once dirname(__FILE__) . '/../../../../table/default_external_repository_gallery_object_table_property_model.class.php';

class MatterhornExternalRepositoryGalleryTablePropertyModel extends DefaultExternalRepositoryGalleryObjectTablePropertyModel
{
    function MatterhornExternalRepositoryGalleryTablePropertyModel()
    {
        parent :: __construct();

        $youtube_properties = MatterhornExternalRepositoryConnector :: get_sort_properties();

        foreach (MatterhornExternalRepositoryConnector :: get_sort_properties() as $property)
        {
            $this->add_property(new GalleryObjectTableProperty($property));
        }
    }
}
?>
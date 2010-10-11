<?php
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
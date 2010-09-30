<?php
/**
 * $Id: photo_gallery_data_manager.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.photo_gallery
 */
interface PhotoGalleryDataManagerInterface
{

    function initialize();

//    function create_storage_unit($name, $properties, $indexes);

    function count_photo_gallery_publications($conditions = null);

    function retrieve_photo_gallery_publication($id);

    function retrieve_photo_gallery_publications($condition = null, $offset = null, $count = null, $order_property = array());
}
?>
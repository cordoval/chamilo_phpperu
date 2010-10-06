<?php
/**
 * $Id: handbook_data_manager.class.php 206 2009-11-13 13:08:01Z chellee $
 * @package application.handbook
 */
/**
 *	This is a skeleton for a data manager for the Portfolio Application.
 *	Data managers must extend this class and implement its methods.
 *
 *	@author Sven Vanpoucke
 */
interface HandbookDataManagerInterface
{

    function initialize();
   
    function create_storage_unit($name, $properties, $indexes);
    function any_content_object_is_published($object_ids);
    function create_handbook_publication($handbook_publication);

    function update_handbook_publication($handbook_publication);
    function update_handbook_information($handbook_information);
    function delete_handbook_publication($handbook_publication);

    function count_handbook_publications($conditions = null);

//    function retrieve_handbook_publications($condition, $offset, $max_objects, $order_by);


    function retrieve_handbook_publication($id);
  
    function create_handbook_information($handbook_publication);
    function create_handbook_publication_group($handbook_publication_group);

   
    function content_object_is_published($object_id);

    function get_content_object_publication_attributes($object_id, $type = null, $offset = null, $count = null, $order_property = null);

    function get_content_object_publication_attribute($publication_id);

    function count_publication_attributes($user = null, $object_id = null, $condition = null);

    function delete_content_object_publications($object_id);

    function update_content_object_publication_id($publication_attr);
    function get_handbook_children($handbook_id);



}
?>
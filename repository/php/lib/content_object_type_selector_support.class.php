<?php
namespace repository;

/**
 * @author Hans De Bisschop
 */
interface ContentObjectTypeSelectorSupport
{

    /**
     * @param string $type
     * @return string
     */
    function get_content_object_type_creation_url($type);

    /**
     * @param string $type
     * @return boolean
     */
    function is_allowed_to_create($type);
    
    /**
     * @return int
     */
    function get_user_id();
}
?>
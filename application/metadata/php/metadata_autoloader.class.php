<?php
namespace application\metadata;
use common\libraries\Utilities;
use common\libraries\WebApplication;
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class MetadataAutoloader
{
    static function load($classname)
    {
        $classname_parts = explode('\\', $classname);

        if (count($classname_parts) == 1)
        {
            return false;
        }
        else
        {
            $classname = $classname_parts[count($classname_parts) - 1];
            array_pop($classname_parts);
            if (implode('\\', $classname_parts) != __NAMESPACE__)
            {
                return false;
            }
        }

        $list = array(
        'metadata_property_type' => 'metadata_property_type.class.php',
        'metadata_property_value' => 'metadata_property_value.class.php',
        'content_object_metadata_property_value' => 'content_object_metadata_property_value.class.php',
        'user_metadata_property_value' => 'user_metadata_property_value.class.php',
        'metadata_namespace' =>'metadata_namespace.class.php',
        'metadata_attribute_nesting' => 'metadata_attribute_nesting.class.php',
        'content_object_property_metadata' => 'content_object_property_metadata.class.php',
        'metadata_data_manager_interface' => 'metadata_data_manager_interface.class.php',
        'metadata_data_manager' => 'metadata_data_manager.class.php',
        'metadata_default_value' => 'metadata_default_value.class.php',
        'metadata_property_attribute_type' => 'metadata_property_attribute_type.class.php',
        'metadata_property_attribute_value' => 'metadata_property_attribute_value.class.php',
        'metadata_property_nesting' => 'metadata_property_nesting.class.php',
        'metadata_manager' => 'metadata_manager/metadata_manager.class.php',
        'content_object_property_metadata_form' => 'forms/content_object_property_metadata_form.class.php',
        'metadata_associations_form' => 'forms/metadata_associations_form.class.php',
        'metadata_default_value_form' => 'forms/metadata_default_value_form.class.php',
        'metadata_form' => 'forms/metadata_form.class.php',
        'content_object_metadata_editor_form' => 'forms/content_object_metadata_editor_form.class.php',
        'user_metadata_editor_form' => 'forms/user_metadata_editor_form.class.php',
        'metadata_namespace_form' => 'forms/metadata_namespace_form.class.php',
        'metadata_property_type_form' => 'forms/metadata_property_type_form.class.php',
        'metadata_property_attribute_type_form' => 'forms/metadata_property_attribute_type_form.class.php',
        'content_object_property_metadata_browser_table' => 'metadata_manager/component/content_object_property_metadata_browser/content_object_property_metadata_browser_table.class.php',
        'metadata_default_value_browser_table_' => 'metadata_manager/component/metadata_default_value_browser/metadata_default_value_browser_table.class.php',
        'metadata_namespace_browser_table' => 'metadata_manager/component/metadata_namespace_browser/metadata_namespace_browser_table.class.php',
        'metadata_property_attribute_type_browser_table' => 'metadata_manager/component/metadata_property_attribute_type_browser/metadata_property_attribute_type_browser_table.class.php',
        'content_object_metadata_property_value_browser_table' => 'metadata_manager/component/content_object_metadata_property_value_browser/content_object_metadata_property_value_browser_table.class.php',
        'user_metadata_property_value_browser_table' => 'metadata_manager/component/user_metadata_property_value_browser/user_metadata_property_value_browser_table.class.php',
        'metadata_property_type_browser_table' => 'metadata_manager/component/metadata_property_type_browser/metadata_property_type_browser_table.class.php'
    );

    $lower_case = Utilities :: camelcase_to_underscores($classname);

    if (key_exists($lower_case, $list))
    {
        $url = $list[$lower_case];
        require_once WebApplication :: get_application_class_lib_path('metadata') . $url;
        return true;
    }

    return false;
    }
}
?>
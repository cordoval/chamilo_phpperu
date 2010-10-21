<?php
/**
 * $Id: user_autoloader.class.php 167 2009-11-12 11:17:52Z vanpouckesven $
 * @author vanpouckesven
 * @package group
 */

class MetadataAutoloader
{
    static function load($classname)
    {
            $list = array(
    'metadata_property_type' => 'metadata_property_type.class.php',
    'metadata_property_value' => 'metadata_property_value.class.php',
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
    'metadata_default_value_form' => 'forms/metadata_default_value_form.class.php.class.php',
    'metadata_form' => 'forms/metadata_form.class.php',
    'metadata_namespace_form' => 'forms/metadata_namespace_form.class.php',
    'metadata_property_attribute_type' => 'forms/metadata_property_attribute_type.class.php',
    'metadata_property_type_form' => 'forms/metadata_property_type_form.class.php',
    'default_content_object_property_metadata_table_cell_renderer' => 'default_content_object_property_metadata_table/default_content_object_property_metadata_table_cell_renderer.class.php',
    'default_content_object_property_metadata_table_column_model' => 'default_content_object_property_metadata_table/default_content_object_property_metadata_table_columns_model.class.php',
    'default_metadata_default_value_table_cell_renderer' => 'default_metadata_default_value_table/default_metadata_default_value_table_cell_renderer.class.php',
    'default_metadata_default_value_table_column_model' => 'default_metadata_default_value_table/default_metadata_default_value_table_columns_model.class.php',
    'default_metadata_namespace_table_cell_renderer' => 'default_metadata_namespace_table/default_metadata_namespace_table_cell_renderer.class.php',
    'default_metadata__namespace_table_column_model' => 'default_metadata_namespace_table/default_metadata_namespace_table_columns_model.class.php',
    'default_metadata_property_attribute_type_table_cell_renderer' => 'default_metadata_property_attribute_type_table/default_metadata_property_attribute_type_table_cell_renderer.class.php',
    'default_metadata_property_attribute_type_table_column_model' => 'default_metadata_property_attribute_type_table/default_metadata_property_attribute_type_table_columns_model.class.php','' => '.class.php',
    'default_metadata_property_type_value_cell_renderer' => 'default_metadata_property_value_table/default_metadata_property_value_table_table_cell_renderer.class.php',
    'default_metadata_property_value_table_column_model' => 'default_metadata_property_value_table/default_metadata_property_value_table_columns_model.class.php',
    'default_metadata_property_type_table_cell_renderer' => 'default_metadata_property_type_table/default_metadata_property_type_table_cell_renderer.class.php',
    'default_metadata_property_type_table_column_model' => 'default_metadata_property_type_table/default_metadata_property_type_table_columns_model.class.php',
    'content_object_property_metadata_table_cell_renderer' => 'content_object_property_metadata_table/default_content_object_property_metadata_table_cell_renderer.class.php',
    'content_object_property_metadata_table_column_model' => 'content_object_property_metadata_table/default_content_object_property_metadata_table_columns_model.class.php',
    'content_object_property_metadata_table' => 'content_object_property_metadata_table/content_object_property_metadata_table.class.php',
    'content_object_property_metadata_table_data_provider' => 'content_object_property_metadata_table/content_object_property_metadata_table_data_provider.class.php',
    'metadata_default_value_table_' => 'metadata_default_value_table/metadata_default_value_table_cell_renderer.class.php',
    'metadata_default_value_table_data_provider' => 'metadata_default_value_table/metadata_default_value_table.class.php',
    'metadata_default_value_table_cell_renderer' => 'metadata_default_value_table/metadata_default_value_table_data_provider.class.php',
    'metadata_default_value_table_column_model' => 'metadata_default_value_table/metadata_default_value_table_columns_model.class.php',
    'metadata_namespace_table' => 'metadata_namespace_table/metadata_namespace_table.class.php',
    'metadata_namespace_table_data_provider' => 'metadata_namespace_table/metadata_namespace_table_data_provider.class.php',
    'metadata_namespace_table_cell_renderer' => 'metadata_namespace_table/metadata_namespace_table_cell_renderer.class.php',
    'metadata_namespace_table_column_model' => 'metadata_namespace_table/metadata_namespace_table_columns_model.class.php',
    'metadata_property_attribute_type_table' => 'metadata_property_attribute_type_table/metadata_property_attribute_type_table.class.php',
    'metadata_property_attribute_type_table_data_provider' => 'metadata_property_attribute_type_table/metadata_property_attribute_type_table_data_provider.class.php',
    'metadata_property_attribute_type_table_cell_renderer' => 'metadata_property_attribute_type_table/metadata_property_attribute_type_table_cell_renderer.class.php',
    'metadata_property_attribute_type_table_column_model' => 'metadata_property_attribute_type_table/metadata_property_attribute_type_table_columns_model.class.php','' => '.class.php',
    'metadata_property_value_table' => 'metadata_property_value_table/metadata_property_value_table.class.php',
    'metadata_property_value_table_data_provider' => 'metadata_property_value_table/metadata_property_value_table_table_data_provider.class.php',
    'metadata_property_value_table_cell_renderer' => 'metadata_property_value_table/metadata_property_value_table_table_cell_renderer.class.php',
    'metadata_property_value_table_column_model' => 'metadata_property_value_table/metadata_property_value_table_columns_model.class.php',
    'metadata_property_type_table' => 'metadata_property_type_table/metadata_property_type_table.class.php',
    'metadata_property_type_table_data_provider' => 'metadata_property_type_table/metadata_property_type_table_data_provider.class.php',
    'metadata_property_type_table_cell_renderer' => 'metadata_property_type_table/metadata_property_type_table_cell_renderer.class.php',
    'metadata_property_type_table_column_model' => 'metadata_property_type_table/metadata_property_type_table_columns_model.class.php'
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
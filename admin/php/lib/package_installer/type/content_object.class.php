<?php
namespace admin;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Translation;
use repository\RepositoryDataManager;
use common\libraries\Filesystem;
use DOMDocument;
use repository\ContentObjectInstaller;

require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';

/**
 * $Id: content_object.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_installer.type
 */

class PackageInstallerContentObjectType extends PackageInstallerType
{

    function install()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $object_name = $attributes->get_code();

        if ($this->verify_dependencies())
        {
            $this->get_parent()->installation_successful('dependencies', Translation :: get('ContentObjectDependenciesVerified'));

            /**********************************************
             * Do the actual install of the objects here. *
             **********************************************/
            $installer = ContentObjectInstaller :: factory($object_name);
            if ($installer)
            {
                if (! $installer->install())
                {
                    return $this->get_parent()->installation_failed('installation', Translation :: get('ContenObjectInstallationFailed'));
                }
                else
                {
                    $this->add_message($installer->retrieve_message());
                    $this->installation_successful('content_object');
                }

                unset($installer);
                flush();
            }
        }
        else
        {
            return $this->get_parent()->installation_failed('dependencies', Translation :: get('PackageDependenciesFailed'));
        }

        $source->cleanup();

        return true;
    }

    static function get_path($content_object_name)
    {
        return Path :: get_repository_content_object_path() . $content_object_name . '/';
    }

    function add_registration()
    {
        $source = $this->get_source();
        $attributes = $source->get_attributes();
        $object_name = $attributes->get_code();

        $registration = new Registration();
        $registration->set_type(Registration :: TYPE_CONTENT_OBJECT);
        $registration->set_name($attributes->get_code());
        $registration->set_category($attributes->get_category());
        $registration->set_status(Registration :: STATUS_ACTIVE);
        $registration->set_version($attributes->get_version());

        return $registration->create();
    }

    function create_storage_unit($path)
    {
        $rdm = RepositoryDataManager :: get_instance();
        $storage_unit_info = self :: parse_xml_file($path);
        $this->get_parent()->add_message(Translation :: get('StorageUnitCreation', array(), Utilities :: COMMON_LIBRARIES) . ': <em>' . $storage_unit_info['name'] . '</em>');
        if (! $rdm->create_storage_unit($storage_unit_info['name'], $storage_unit_info['properties'], $storage_unit_info['indexes']))
        {
            return $this->get_parent()->installation_failed(Translation :: get('StorageUnitCreationFailed', array(), Utilities :: COMMON_LIBRARIES) . ': <em>' . $storage_unit_info['name'] . '</em>');
        }
        else
        {
            return true;
        }
    }

    /**
     * Parses an XML file describing a storage unit.
     * For defining the 'type' of the field, the same definition is used as the
     * PEAR::MDB2 package. See http://pear.php.net/manual/en/package.database.
     * mdb2.datatypes.php
     * @param string $file The complete path to the XML-file from which the
     * storage unit definition should be read.
     * @return array An with values for the keys 'name','properties' and
     * 'indexes'
     */
    public static function parse_xml_file($file)
    {
        $doc = new DOMDocument();
        $doc->load($file);
        $object = $doc->getElementsByTagname('object')->item(0);
        $name = $object->getAttribute('name');
        $xml_properties = $doc->getElementsByTagname('property');
        $attributes = array('type', 'length', 'unsigned', 'notnull', 'default', 'autoincrement', 'fixed');
        foreach ($xml_properties as $index => $property)
        {
            $property_info = array();
            foreach ($attributes as $index => $attribute)
            {
                if ($property->hasAttribute($attribute))
                {
                    $property_info[$attribute] = $property->getAttribute($attribute);
                }
            }
            $properties[$property->getAttribute('name')] = $property_info;
        }
        $xml_indexes = $doc->getElementsByTagname('index');
        foreach ($xml_indexes as $key => $index)
        {
            $index_info = array();
            $index_info['type'] = $index->getAttribute('type');
            $index_properties = $index->getElementsByTagname('indexproperty');
            foreach ($index_properties as $subkey => $index_property)
            {
                $index_info['fields'][$index_property->getAttribute('name')] = array('length' => $index_property->getAttribute('length'));
            }
            $indexes[$index->getAttribute('name')] = $index_info;
        }
        $result = array();
        $result['name'] = $name;
        $result['properties'] = $properties;
        $result['indexes'] = $indexes;

        return $result;
    }
}
?>
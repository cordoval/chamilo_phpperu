<?php
namespace application\package;

use common\libraries;

use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\DataClass;
use common\libraries\NotCondition;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\InCondition;
use common\libraries\Text;
use common\libraries\Filesystem;
use common\libraries\StringUtilities;
use common\libraries\WebApplication;
use common\libraries\PlatformSetting;

use admin;

/**
 * This class describes a Package data object
 *
 * $Id: remote_package.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib
 * @author Hans De Bisschop
 */
class Package extends DataClass
{
    const CLASS_NAME = __CLASS__;
    
    /**
     * Package properties
     */
    const PROPERTY_CODE = 'code';
    const PROPERTY_NAME = 'name';
    const PROPERTY_SECTION = 'section';
    const PROPERTY_CATEGORY = 'category';
    const PROPERTY_VERSION = 'version';
    //    const PROPERTY_CYCLE = 'cycle';
    const PROPERTY_FILENAME = 'filename';
    const PROPERTY_SIZE = 'size';
    const PROPERTY_MD5 = 'md5';
    const PROPERTY_SHA1 = 'sha1';
    const PROPERTY_SHA256 = 'sha256';
    const PROPERTY_SHA512 = 'sha512';
    const PROPERTY_TAGLINE = 'tagline';
    const PROPERTY_DESCRIPTION = 'description';
    const PROPERTY_HOMEPAGE = 'homepage';
    //    const PROPERTY_DEPENDENCIES = 'dependencies';
    const PROPERTY_EXTRA = 'extra';
    const PROPERTY_STATUS = 'status';
    
    // Sub-properties
    const PROPERTY_CYCLE_PHASE = 'cycle_phase';
    const PROPERTY_CYCLE_REALM = 'cycle_realm';
    
    // Release phases
    const PHASE_ALPHA = 'alpha';
    const PHASE_BETA = 'beta';
    const PHASE_RELEASE_CANDIDATE = 'release_candidate';
    const PHASE_GENERAL_AVAILABILITY = 'general_availability';
    
    // Release realm
    const REALM_MAIN = 'main';
    const REALM_UNIVERSE = 'universe';
    
    //status
    const STATUS_PENDING = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_REJECTED = 3;
    
    //sections
    const TYPE_APPLICATIONS = 1;
    const TYPE_EXTENSIONS = 2;
    const TYPE_CONTENT_OBJECTS = 3;
    const TYPE_EXTERNAL_REPOSITORY_MANAGER = 4;
    const TYPE_VIDEO_CONFERENCING = 5;
    const TYPE_LIBRARY = 6;
    const TYPE_CORE = 7;
    
    const AUTHORS = 'authors';
    const DEPENDENCIES = 'dependencies';
    
    private $temporary_file_path;

    /**
     * Get the default properties
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_CODE, 
                self :: PROPERTY_NAME, 
                self :: PROPERTY_SECTION, 
                self :: PROPERTY_CATEGORY, 
                //                self :: PROPERTY_AUTHORS, 
                self :: PROPERTY_VERSION, 
                self :: PROPERTY_FILENAME, 
                self :: PROPERTY_SIZE, 
                self :: PROPERTY_MD5, 
                self :: PROPERTY_SHA1, 
                self :: PROPERTY_SHA256, 
                self :: PROPERTY_SHA512, 
                self :: PROPERTY_TAGLINE, 
                self :: PROPERTY_DESCRIPTION, 
                self :: PROPERTY_HOMEPAGE, 
                //                self :: PROPERTY_DEPENDENCIES, 
                self :: PROPERTY_EXTRA, 
                self :: PROPERTY_CYCLE_PHASE, 
                self :: PROPERTY_CYCLE_REALM, 
                self :: PROPERTY_STATUS));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PackageDataManager :: get_instance();
    }

    public function set_temporary_file_path($temporary_file_path)
    {
        if (StringUtilities :: has_value($temporary_file_path))
        {
            $this->temporary_file_path = $temporary_file_path;
        }
    }

    function save_file()
    {
        $filename_hash = md5($this->get_filename());
        $relative_folder_path = Text :: char_at($filename_hash, 0);
        $full_folder_path = WebApplication :: get_application_path('package') . 'files/' . $relative_folder_path;
        
        Filesystem :: create_dir($full_folder_path);
        $unique_hash = Filesystem :: create_unique_name($full_folder_path, $filename_hash);
        
        $relative_path = $relative_folder_path . '/' . $unique_hash;
        $path_to_save = $full_folder_path . '/' . $unique_hash;
        
        $save_success = false;
        if (StringUtilities :: has_value($this->temporary_file_path))
        {
            if (Filesystem :: move_file($this->temporary_file_path, $path_to_save))
            {
                $save_success = true;
            }
            else
            {
                if (FileSystem :: copy_file($this->temporary_file_path, $path_to_save))
                {
                    if (FileSystem :: remove($this->temporary_file_path))
                    {
                        $save_success = true;
                    }
                }
            }
        
        }
        elseif (StringUtilities :: has_value($this->in_memory_file) && Filesystem :: write_to_file($path_to_save, $this->in_memory_file))
        {
            $save_success = true;
        }
        
        if ($save_success)
        {
            Filesystem :: chmod($path_to_save, PlatformSetting :: get('permissions_new_files'));
            
            $file_bytes = Filesystem :: get_disk_space($path_to_save);
            
            $this->set_size($file_bytes);
            //            $this->set_path($relative_path);
            $this->set_md5($unique_hash);
            //            $this->set_content_hash(md5_file($path_to_save));
        }
        else
        {
            $this->add_error(Translation :: get('DocumentStoreError'));
        }
        
        return $save_success;
    }

    /**
     * Returns the code of this Package.
     * @return the code.
     */
    function get_code()
    {
        return $this->get_default_property(self :: PROPERTY_CODE);
    }

    /**
     * Sets the code of this Package.
     * @param code
     */
    function set_code($code)
    {
        $this->set_default_property(self :: PROPERTY_CODE, $code);
    }

    /**
     * Returns the name of this Package.
     * @return the name.
     */
    function get_name()
    {
        return $this->get_default_property(self :: PROPERTY_NAME);
    }

    /**
     * Sets the name of this Package.
     * @param name
     */
    function set_name($name)
    {
        $this->set_default_property(self :: PROPERTY_NAME, $name);
    }

    /**
     * Returns the section of this Package.
     * @return the section.
     */
    function get_section()
    {
        return $this->get_default_property(self :: PROPERTY_SECTION);
    }

    /**
     * Sets the section of this Package.
     * @param section
     */
    function set_section($section)
    {
        $this->set_default_property(self :: PROPERTY_SECTION, $section);
    }

    /**
     * Returns the category of this Package.
     * @return the category.
     */
    function get_category()
    {
        return $this->get_default_property(self :: PROPERTY_CATEGORY);
    }

    /**
     * Sets the category of this Package.
     * @param category
     */
    function set_category($category)
    {
        $this->set_default_property(self :: PROPERTY_CATEGORY, $category);
    }

    /**
     * Returns the authors of this Package.
     * @return the authors.
     */
    function get_authors($only_ids = true)
    {
        $condition = new EqualityCondition(PackageAuthor :: PROPERTY_PACKAGE_ID, $this->get_id());
        $package_authors = $this->get_data_manager()->retrieve_package_authors($condition);
        
        $package_authors_ids = array();
        
        while ($package_author = $package_authors->next_result())
        {
            $package_authors_ids[] = $package_author->get_author_id();
        }
        
        if ($only_ids)
        {
            return $package_authors_ids;
        }
        else
        {
            $package_authors_ids[] = - 1;
            $condition = new InCondition(Author :: PROPERTY_ID, $package_authors_ids);
            return $this->get_data_manager()->retrieve_authors($condition);
        }
    }

    /**
     * Returns the version of this Package.
     * @return the version.
     */
    function get_version()
    {
        return $this->get_default_property(self :: PROPERTY_VERSION);
    }

    /**
     * Sets the version of this Package.
     * @param version
     */
    function set_version($version)
    {
        $this->set_default_property(self :: PROPERTY_VERSION, $version);
    }

    /**
     * Returns the cycle phase of this Package.
     * @return the cycle phase.
     */
    function get_cycle_phase()
    {
        return $this->get_default_property(self :: PROPERTY_CYCLE_PHASE);
    }

    function set_cycle_phase($phase_cycle)
    {
        $this->set_default_property(self :: PROPERTY_CYCLE_PHASE, $phase_cycle);
    }

    /**
     * Returns the cycle realm of this Package.
     * @return the cycle realm.
     */
    function get_cycle_realm()
    {
        return $this->get_default_property(self :: PROPERTY_CYCLE_REALM);
    }

    function set_cycle_realm($realm_cycle)
    {
        $this->set_default_property(self :: PROPERTY_CYCLE_REALM, $realm_cycle);
    }

    static function get_phases()
    {
        return array(self :: PHASE_ALPHA => Translation :: get(self :: PHASE_ALPHA), 
                self :: PHASE_BETA => Translation :: get(self :: PHASE_BETA), 
                self :: PHASE_GENERAL_AVAILABILITY => Translation :: get(self :: PHASE_GENERAL_AVAILABILITY), 
                self :: PHASE_RELEASE_CANDIDATE => Translation :: get(self :: PHASE_RELEASE_CANDIDATE));
    }

    static function get_realms()
    {
        return array(self :: REALM_MAIN => Translation :: get(self :: REALM_MAIN), 
                self :: REALM_UNIVERSE => Translation :: get(self :: REALM_UNIVERSE));
    }

    /**
     * Returns the filename of this Package.
     * @return the filename.
     */
    function get_filename()
    {
        return $this->get_default_property(self :: PROPERTY_FILENAME);
    }

    /**
     * Sets the filename of this Package.
     * @param filename
     */
    function set_filename($filename)
    {
        $this->set_default_property(self :: PROPERTY_FILENAME, $filename);
    }

    /**
     * Returns the size of this Package.
     * @return the size.
     */
    function get_size()
    {
        return $this->get_default_property(self :: PROPERTY_SIZE);
    }

    /**
     * Sets the size of this Package.
     * @param size
     */
    function set_size($size)
    {
        $this->set_default_property(self :: PROPERTY_SIZE, $size);
    }

    /**
     * Returns the md5 of this Package.
     * @return the md5.
     */
    function get_md5()
    {
        return $this->get_default_property(self :: PROPERTY_MD5);
    }

    /**
     * Sets the md5 of this Package.
     * @param md5
     */
    function set_md5($md5)
    {
        $this->set_default_property(self :: PROPERTY_MD5, $md5);
    }

    /**
     * Returns the sha1 of this Package.
     * @return the sha1.
     */
    function get_sha1()
    {
        return $this->get_default_property(self :: PROPERTY_SHA1);
    }

    /**
     * Sets the sha1 of this Package.
     * @param sha1
     */
    function set_sha1($sha1)
    {
        $this->set_default_property(self :: PROPERTY_SHA1, $sha1);
    }

    /**
     * Returns the sha256 of this Package.
     * @return the sha256.
     */
    function get_sha256()
    {
        return $this->get_default_property(self :: PROPERTY_SHA256);
    }

    /**
     * Sets the sha256 of this Package.
     * @param sha256
     */
    function set_sha256($sha256)
    {
        $this->set_default_property(self :: PROPERTY_SHA256, $sha256);
    }

    /**
     * Returns the sha512 of this Package.
     * @return the sha512.
     */
    function get_sha512()
    {
        return $this->get_default_property(self :: PROPERTY_SHA512);
    }

    /**
     * Sets the sha512 of this Package.
     * @param sha512
     */
    function set_sha512($sha512)
    {
        $this->set_default_property(self :: PROPERTY_SHA512, $sha512);
    }

    /**
     * Returns the tagline of this Package.
     * @return the tagline.
     */
    function get_tagline()
    {
        return $this->get_default_property(self :: PROPERTY_TAGLINE);
    }

    /**
     * Sets the tagline of this Package.
     * @param tagline
     */
    function set_tagline($tagline)
    {
        $this->set_default_property(self :: PROPERTY_TAGLINE, $tagline);
    }

    /**
     * Returns the description of this Package.
     * @return the description.
     */
    function get_description()
    {
        return $this->get_default_property(self :: PROPERTY_DESCRIPTION);
    }

    /**
     * Sets the description of this Package.
     * @param description
     */
    function set_description($description)
    {
        $this->set_default_property(self :: PROPERTY_DESCRIPTION, $description);
    }

    /**
     * Returns the extras of this Package.
     * @return the extras.
     */
    function get_extra()
    {
        return unserialize($this->get_default_property(self :: PROPERTY_EXTRA));
    }

    /**
     * Sets the extras of this Package.
     * @param extras
     */
    function set_extra($extra)
    {
        $this->set_default_property(self :: PROPERTY_EXTRA, serialize($extra));
    }

    /**
     * Returns the status of this Package.
     * @return the status.
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Sets the status of this Package.
     * @param status
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    function get_status_string()
    {
        switch ($this->get_status())
        {
            case 1 :
                return Translation :: get('Pending');
                break;
            case 2 :
                return Translation :: get('Accepted');
                break;
            case 3 :
                return Translation :: get('Rejected');
                break;
        }
    }

    /**
     * Returns the homepage of this Package.
     * @return the homepage.
     */
    function get_homepage()
    {
        return $this->get_default_property(self :: PROPERTY_HOMEPAGE);
    }

    /**
     * Sets the homepage of this Package.
     * @param homepage
     */
    function set_homepage($homepage)
    {
        $this->set_default_property(self :: PROPERTY_HOMEPAGE, $homepage);
    }

    static function get_section_types()
    {
        $types = array();
        $types[self :: TYPE_APPLICATIONS] = self :: get_section_type_name(self :: TYPE_APPLICATIONS);
        $types[self :: TYPE_CONTENT_OBJECTS] = self :: get_section_type_name(self :: TYPE_CONTENT_OBJECTS);
        $types[self :: TYPE_EXTENSIONS] = self :: get_section_type_name(self :: TYPE_EXTENSIONS);
        $types[self :: TYPE_EXTERNAL_REPOSITORY_MANAGER] = self :: get_section_type_name(self :: TYPE_EXTERNAL_REPOSITORY_MANAGER);
        $types[self :: TYPE_VIDEO_CONFERENCING] = self :: get_section_type_name(self :: TYPE_VIDEO_CONFERENCING);
        $types[self :: TYPE_LIBRARY] = self :: get_section_type_name(self :: TYPE_LIBRARY);
        $types[self :: TYPE_CORE] = self :: get_section_type_name(self :: TYPE_CORE);
        return $types;
    }

    static function get_section_type_name($type)
    {
        switch ($type)
        {
            case self :: TYPE_APPLICATIONS :
                return Translation :: get('Applications');
                break;
            case self :: TYPE_CONTENT_OBJECTS :
                return Translation :: get('ContentObjects');
                break;
            case self :: TYPE_EXTENSIONS :
                return Translation :: get('Extensions');
                break;
            case self :: TYPE_EXTERNAL_REPOSITORY_MANAGER :
                return Translation :: get('ExternalRepositoryManager');
                break;
            case self :: TYPE_VIDEO_CONFERENCING :
                return Translation :: get('VideoConferencing');
                break;
            case self :: TYPE_LIBRARY :
                return Translation :: get('Library');
                break;
            case self :: TYPE_CORE :
                return Translation :: get('Core');
                break;
        }
    }

    /**
     * Returns the dependencies of this Package.
     * @return the dependencies.
     */
    function get_dependencies($only_ids = true)
    {
        $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_PACKAGE_ID, $this->get_id());
        $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_TYPE, PackageDependency :: TYPE_DEPENDENCY);
        $condition = new AndCondition($conditions);
        
        $package_dependencies = $this->get_data_manager()->retrieve_package_dependencies($condition);
        
        if ($only_ids)
        {
            $package_dependencies_ids = array();
            while ($package_dependency = $package_dependencies->next_result())
            {
                $package_dependencies_ids[] = $package_dependency->get_id();
            }
            return $package_dependencies_ids;
        }
        else
        {
            return $package_dependencies;
        }
    }

    function get_dependencies_ids()
    {
        $dependencies = $this->get_dependencies(false);
        $package_dependencies_ids = array();
        while ($package_dependency = $dependencies->next_result())
        {
            $package_dependencies_ids[] = $package_dependency->get_dependency_id();
        }
        return $package_dependencies_ids;
    }

    function get_package_dependencies($only_ids = true)
    {
        $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_PACKAGE_ID, $this->get_id());
        $conditions[] = new EqualityCondition(PackageDependency :: PROPERTY_DEPENDENCY_TYPE, PackageDependency :: TYPE_PACKAGE);
        $condition = new AndCondition($conditions);
        
        $package_dependencies = $this->get_data_manager()->retrieve_package_dependencies($condition);
        
        if ($only_ids)
        {
            $package_dependencies_ids = array();
            while ($package_dependency = $package_dependencies->next_result())
            {
                $package_dependencies_ids[] = $package_dependency->get_id();
            }
            return $package_dependencies_ids;
        }
        else
        {
            return $package_dependencies;
        }
    }

    function get_package_dependencies_ids()
    {
        $dependencies = $this->get_package_dependencies(false);
        $package_dependencies_ids = array();
        while ($package_dependency = $dependencies->next_result())
        {
            $package_dependencies_ids[] = $package_dependency->get_dependency_id();
        }
        return $package_dependencies_ids;
    }

    static function get_table_name()
    {
        return Utilities :: get_classname_from_namespace(self :: CLASS_NAME, true);
    }

    function is_official()
    {
        return $this->get_cycle_realm() == self :: REALM_MAIN;
    }

    function is_stable()
    {
        return $this->get_cycle_phase() == self :: PHASE_GENERAL_AVAILABILITY;
    }

    function create()
    {
        $succes = parent :: create();
        $this->save_file();
        
        return $succes;
    }

    function update()
    {
        $dm = $this->get_data_manager();
        
        $condition = new NotCondition(new EqualityCondition(Package :: PROPERTY_ID, $this->get_id()));
        $packages = $dm->retrieve_packages($condition);
        
        return parent :: update();
    }

    function delete()
    {
        $succes = parent :: delete();
        $dm = $this->get_data_manager();
        
        return $succes;
    
    }

}

?>
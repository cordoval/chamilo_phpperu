<?php

namespace application\package;

use common\libraries;

use common\libraries\Configuration;
use common\libraries\WebApplication;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\EqualityCondition;
use common\libraries\Filesystem;

use admin\Registration;

use DOMDocument;
/**
 * This is a skeleton for a data manager for the Package Application.
 * Data managers must extend this class and implement its abstract methods.
 *
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */

class PackageDataManager
{
    /**
     * Instance of this class for the singleton pattern.
     */
    private static $instance;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->initialize();
    }

    /**
     * Uses a singleton pattern and a factory pattern to return the data
     * manager. The configuration determines which data manager class is to
     * be instantiated.
     * @return PackageDataManager The data manager.
     */
    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            $type = Configuration :: get_instance()->get_parameter('general', 'data_manager');
            require_once WebApplication :: get_application_class_lib_path('package') . 'data_manager/' . strtolower($type) . '_package_data_manager.class.php';
            $class = __NAMESPACE__ . '\\' . Utilities :: underscores_to_camelcase($type) . 'PackageDataManager';
            self :: $instance = new $class();
        }
        return self :: $instance;
    }

    static function generate_packages_xml()
    {
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->formatOutput = true;
        
        $parent = $doc->createElement('packages');
        $doc->appendChild($parent);
        
        $packages_data = PackageDataManager :: get_instance()->retrieve_packages();
        while ($package_data = $packages_data->next_result())
        {
            $root = $doc->createElement('package');
            $parent->appendChild($root);
            
            //package
            $package = $doc->createElement('name', $package_data->get_name());
            $root->appendChild($package);
            
            $package = $doc->createElement('code', $package_data->get_code());
            $root->appendChild($package);
            
            $package = $doc->createElement('section', $package_data->get_section());
            $root->appendChild($package);
            
            $package = $doc->createElement('category', $package_data->get_category());
            $root->appendChild($package);
            
            $package = $doc->createElement('version', $package_data->get_version());
            $root->appendChild($package);
            
            //package/cycle
            $package = $doc->createElement('cycle');
            $root->appendChild($package);
            
            $cycle = $doc->createElement('phase', $package_data->get_cycle_phase());
            $package->appendChild($cycle);
            
            $cycle = $doc->createElement('realm', $package_data->get_cycle_realm());
            $package->appendChild($cycle);
            
            $package = $doc->createElement('description', $package_data->get_description());
            $root->appendChild($package);
            
            $package = $doc->createElement('homepage', $package_data->get_homepage());
            $root->appendChild($package);
            
            $package = $doc->createElement('tagline', $package_data->get_tagline());
            $root->appendChild($package);
            
            //package/authors
            $package = $doc->createElement('authors');
            $root->appendChild($package);
            
            $authors_data = $package_data->get_authors(false);
            while ($author_data = $authors_data->next_result())
            {
                
                $authors = $doc->createElement('author');
                $package->appendChild($authors);
                
                $author = $doc->createElement('name', $author_data->get_name());
                $authors->appendChild($author);
                
                $author = $doc->createElement('email', $author_data->get_email());
                $authors->appendChild($author);
                
                $author = $doc->createElement('company', $author_data->get_company());
                $authors->appendChild($author);
            }
            
            $dependencies_data = $package_data->get_package_dependencies(false);
            
            //package/dependencies
            $dependencies = $doc->createElement('dependencies');
            $root->appendChild($dependencies);
            
            $server = $doc->createElement('server');
            $dependencies->appendChild($server);
            
            $content_objects = $doc->createElement('content_objects');
            $dependencies->appendChild($content_objects);
            
            $applications = $doc->createElement('applications');
            $dependencies->appendChild($applications);
            
            $core = $doc->createElement('core');
            $dependencies->appendChild($core);
            
            $extension = $doc->createElement('extension');
            $dependencies->appendChild($extension);
            
            $extensions = $doc->createElement('extensions');
            $dependencies->appendChild($extensions);
            
            $external_repository_manager = $doc->createElement('external_repository_manager');
            $dependencies->appendChild($external_repository_manager);
            
            $library = $doc->createElement('library');
            $dependencies->appendChild($library);
            
            $settings = $doc->createElement('settings');
            $dependencies->appendChild($settings);
            
            $video_conferencing_manager = $doc->createElement('video_conferencing_manager');
            $dependencies->appendChild($video_conferencing_manager);
            
            while ($dependency_data = $dependencies_data->next_result())
            {
                $dependency = $doc->createElement('dependency');
                
                $dependency->appendChild($doc->createElement('id', $dependency_data->get_dependency()->get_code()));
                $version = $doc->createElement('version', $dependency_data->get_dependency()->get_version());
                $type = $doc->createAttribute('type');
                $type->appendChild($doc->createTextNode($dependency_data->get_compare()));
                $version->appendChild($type);
                $dependency->appendChild($version);
                
                $dependency->appendChild($doc->createElement('severity', $dependency_data->get_severity()));
                
                $dependencies->appendChild($dependency);
                switch ($dependency_data->get_dependency()->get_section())
                {
                    case Registration :: TYPE_CONTENT_OBJECT :
                        $content_objects->appendChild($dependency);
                        break;
                    case Registration :: TYPE_CORE :
                        $core->appendChild($dependency);
                        break;
                    case Registration :: TYPE_APPLICATION :
                        $applications->appendChild($dependency);
                        break;
                    case Registration :: TYPE_EXTENSION :
                        $extensions->appendChild($dependency);
                        break;
                    case Registration :: TYPE_LIBRARY :
                        $library->appendChild($dependency);
                        break;
                    case Registration :: TYPE_EXTERNAL_REPOSITORY_MANAGER :
                        $external_repository_manager->appendChild($dependency);
                        break;
                    case Registration :: TYPE_VIDEO_CONFERENCING_MANAGER :
                        $video_conferencing_manager->appendChild($dependency);
                        break;
                }
            
            }
        
        }
        
        //create .xml
        $temp_dir = Path::get(SYS_PATH);
               
        $xml_path = $temp_dir . 'packages.xml';
        if (file_exists($xml_path))
        {
            Filesystem::remove($xml_path);
        }
        
        $doc->save($xml_path);
    }
}
?>
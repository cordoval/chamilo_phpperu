<?php
namespace application\package;

use common\libraries;
use common\libraries\EqualityCondition;
use common\libraries\WebApplication;

use admin\Registration;

use DOMDocument;
/**
 * @package application.package.package.component
 */

/**
 * package component which allows the user to browse his package_languages
 * @author Sven Vanpoucke
 * @author Hans De Bisschop
 */
class PackageManagerXmlComponent extends PackageManager
{

    function run()
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
        $temp_dir = WebApplication :: get_application_class_path('package') . 'lib/package_manager/';
        if (! is_dir($temp_dir))
        {
            mkdir($temp_dir, 0777, true);
        }
        
        $xml_path = $temp_dir . 'temp.xml';
        $doc->save($xml_path);
    }
}
?>
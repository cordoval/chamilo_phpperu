<?php
namespace application\package;

use common\libraries\Utilities;
use common\libraries\Application;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\PlatformSetting;
use common\libraries\Breadcrumb;
use common\libraries\NotCondition;
use common\libraries\OrCondition;
use common\libraries\AdministrationComponent;

use PEAR;
use XML_Unserializer;

/**
 * $Id: synchronizer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
class PackageInstanceManagerSynchronizerComponent extends PackageInstanceManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $data = $this->get_packages_data();
        
        if ($data)
        {
            if ($this->parse_packages_data($data))
            {
                $message = 'PackagesListSynchronised';
                $failures = 0;
            }
            else
            {
                $message = 'PackagesDataError';
                $failures = 1;
            }
            
            $this->redirect(Translation :: get($message), ($failures ? true : false), array(
                    
                    PackageInstanceManager :: PARAM_PACKAGE_INSTANCE_ACTION => PackageInstanceManager :: ACTION_BROWSE));
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoValidRemotePackagesList')));
        }
    }

    function get_packages_data()
    {
        $online_repository = PlatformSetting :: get('package_repository');
        
        $xml_data = file_get_contents($online_repository . 'packages.xml');
        
        if ($xml_data)
        {
            $unserializer = new XML_Unserializer();
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array(
                    'package', 
                    'dependency'));
            
            // userialize the document
            $status = $unserializer->unserialize($xml_data);
            
            if (PEAR :: isError($status))
            {
                $this->display_error_page($status->getMessage());
            }
            else
            {
                return $unserializer->getUnserializedData();
            }
        }
        else
        {
            return false;
        }
    }

    function parse_packages_data($data)
    {
        $pdm = PackageDataManager :: get_instance();
        
        $conditions = array();
        
        foreach ($data['package'] as $package)
        {
            $package_conditions = array();
            $package_conditions[] = new EqualityCondition(Package :: PROPERTY_CODE, $package['code']);
            $package_conditions[] = new EqualityCondition(Package :: PROPERTY_SECTION, $package['section']);
            $conditions[] = new AndCondition($package_conditions);
            
            $package['authors'] = serialize($package['authors']);
            $package['cycle'] = $package['cycle'];
            $package['dependencies'] = serialize($package['dependencies']);
            $package['extra'] = serialize($package['extra']);
            
            $condition = new EqualityCondition(Package :: PROPERTY_CODE, $package['code']);
            $packages = $pdm->retrieve_packages($condition, array(), 0);

            if ($packages->size() === 1)
            {
                
                $package_result = $packages->next_result();
                $package['id'] = $package_result->get_id();
                
                $package_result->set_category($package['category']);
                $package_result->set_code($package['code']);
                $package_result->set_cycle_phase($package['cycle']['phase']);
                $package_result->set_description($package['description']);
                $package_result->set_name($package['name']);
                $package_result->set_section($package['section']);
                $package_result->set_version($package['version']);
                $package_result->set_status(Package :: STATUS_ACCEPTED);
                $package_result->set_category($package['category']);
                $package_result->set_size($package['size']);
                
                if (! $package_result->update())
                {
                    return false;
                }
            }
            else
            {
                $package_result = new Package();
                $package_result->set_category($package['category']);
                $package_result->set_code($package['code']);
                $package_result->set_cycle_phase($package['cycle']['phase']);
                $package_result->set_description($package['description']);
                $package_result->set_name($package['name']);
                $package_result->set_section($package['section']);
                $package_result->set_version($package['version']);
                $package_result->set_status(Package :: STATUS_ACCEPTED);
                $package_result->set_category($package['category']);
                $package_result->set_size($package['size']);

                if (! $package_result->create())
                {
                    return false;
                }
            }
        }
        $condition = new NotCondition(new OrCondition($conditions));
        
        return true;
    }

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
        $breadcrumbtrail->add_help('package_synchronizer');
        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(
                PackageInstanceManager :: PARAM_PACKAGE_INSTANCE_ACTION => PackageInstanceManager :: ACTION_BROWSE)), Translation :: get('PackageManagerBrowserComponent')));
    }

    function get_additional_parameters()
    {
        return array(self :: PARAM_PACKAGE_ID);
    }
}
?>
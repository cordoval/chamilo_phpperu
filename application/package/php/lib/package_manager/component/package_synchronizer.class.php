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
 * $Id: package_synchronizer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_manager.component
 * @author Hans De Bisschop
 */
class PackageManagerPackageSynchronizerComponent extends PackageManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
//        if (! AdminRights :: is_allowed(AdminRights :: RIGHT_VIEW))
//        {
//            $this->display_header();
//            $this->display_error_message(Translation :: get('NotAllowed', array(), Utilities :: COMMON_LIBRARIES));
//            $this->display_footer();
//            exit();
//        }

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

            $this->redirect(Translation :: get($message), ($failures ? true : false), array(Application :: PARAM_ACTION => PackageManager :: ACTION_BROWSE_PACKAGE, PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_SYNCHRONIZE_PACKAGE));
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
            $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('package', 'dependency'));

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
            $package_conditions[] = new EqualityCondition(Package :: PROPERTY_CODE,  $package['code']);
            $package_conditions[] = new EqualityCondition(Package :: PROPERTY_SECTION, $package['section']);
            $conditions [] = new AndCondition($package_conditions);

            $package['authors'] = serialize($package['authors']);
            $package['cycle'] = serialize($package['cycle']);
        	$package['dependencies'] = serialize($package['dependencies']);
        	$package['extra'] = serialize($package['extra']);

            $condition = new EqualityCondition(Package :: PROPERTY_CODE, $package['code']);
            $packages = $pdm->retrieve_packages($condition, array(), 0);
            if ($packages->size() === 1)
            {
                $package_result = $packages->next_result();
                $package['id'] = $package_result->get_id();
                $package_result->set_default_properties($package);
                if (! $package_result->update())
                {
                    return false;
                }
            }
            else
            {
            	$package_result = new Package($package);
                if (! $package_result->create())
                {
                	return false;
                }
            }
        }
        $condition = new NotCondition(new OrCondition($conditions));
//        if (! AdminDataManager::get_instance()->delete_remote_packages($condition))
//        {
//        	return false;
//        }
        return true;
    }

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
//    	$breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_BROWSE_PACKAGES)), Translation :: get('PackageManagerBrowserComponent')));
//        $breadcrumbtrail->add(new Breadcrumb($this->get_url(array(PackageManager :: PARAM_PACKAGE_ACTION => PackageManager :: ACTION_REMOTE_PACKAGE, PackageManager :: PARAM_SECTION => Request :: get(PackageManager :: PARAM_SECTION),
//        		PackageManager :: PARAM_PACKAGE => Request :: get(PackageManager :: PARAM_PACKAGE), PackageManager :: PARAM_INSTALL_TYPE => 'remote')), Translation :: get('PackageManagerRemoteComponent')));
//
//    	$breadcrumbtrail->add_help('admin_package_manager_synchroniser');
    }

 	function get_additional_parameters()
    {
//    	return array(PackageManager :: PARAM_INSTALL_TYPE, PackageManager :: PARAM_PACKAGE, PackageManager :: PARAM_SECTION);
    }
}
?>
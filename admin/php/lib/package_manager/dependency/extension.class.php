<?php
namespace admin;

use common\libraries\Utilities;
use common\libraries\Application;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use common\libraries\MessageLogger;

/**
 * $Id: applications.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.dependency
 */

class ExtensionPackageDependency extends PackageDependency
{
    const PROPERTY_VERSION = 'version';

    private $version;

    function __construct($dependency)
    {
        parent :: __construct($dependency);
        $this->set_version($dependency['version']);
    }

    /**
     * @return the $version
     */
    public function get_version()
    {
        return $this->version;
    }

    public function get_operator()
    {
    	return $this->version['type'];
    }

    public function get_version_number()
    {
    	return $this->version['_content'];
    }

    /**
     * @param $version the $version to set
     */
    public function set_version($version)
    {
        $this->version = $version;
    }

    function as_html()
    {
        $version = $this->get_version();
        return Translation :: get('TypeName', null, 'common\extensions\\' . $this->get_id()) . ', ' . Translation :: get('Version', array(), Utilities :: COMMON_LIBRARIES) . ': ' . $version['_content'];
    }

    function check()
    {
        $version = $this->get_version();
        $message = Translation :: get('DependencyCheckExtension') . ': ' . $this->as_html() . ' ' . Translation :: get('Found', array(), Utilities :: COMMON_LIBRARIES) . ': ';

        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $this->get_id());
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_EXTENSION);
        $condition = new AndCondition($conditions);

        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition, array(), 0, 1);

        if ($registrations->size() === 0)
        {
            $message .= '--' . Translation :: get('Nothing', array(), Utilities :: COMMON_LIBRARIES) . '--';
            $this->logger->add_message($message);
            return false;
        }
        else
        {
            $registration = $registrations->next_result();

            $application_version = $this->version_compare($version['type'], $version['_content'], $registration->get_version());
            if (! $application_version)
            {
                $message .= '--' . Translation :: get('WrongVersion', array(), Utilities :: COMMON_LIBRARIES) . '--';
                $this->logger->add_message($message);
                $this->logger->add_message(Translation :: get('DependencyExtensionWrongVersion'), MessageLogger :: TYPE_WARNING);
                return false;
            }
            else
            {
                if (! $registration->is_active())
                {
                    $message .= '--' . Translation :: get('InactiveExtension', array(), Utilities :: COMMON_LIBRARIES) . '--';
                    $this->logger->add_message($message);
                    $this->logger->add_message(Translation :: get('DependencyActivateExtensionWarning'), MessageLogger :: TYPE_WARNING);
                }
                else
                {
                    $message .= $registration->get_version();
                    $this->logger->add_message($message);
                }

                return true;
            }
        }
    }
}
?>
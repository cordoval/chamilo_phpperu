<?php
/**
 * $Id: applications.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.dependency
 */

class ApplicationsPackageDependency extends PackageDependency
{
	const PROPERTY_VERSION = 'version';

	private $version;

	function ApplicationsPackageDependency($dependency)
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
    	return Translation :: get(Utilities :: underscores_to_camelcase($this->get_id())) . ', ' . Translation :: get('Version') . ': ' . $version['_content'];
    }

	function check()
    {
    	$version = $this->get_version();
        $message = Translation :: get('DependencyCheckApplication') . ': ' . $this->as_html() . ' ' . Translation :: get('Found') . ': ';

        $conditions = array();
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_NAME, $this->get_id());
        $conditions[] = new EqualityCondition(Registration :: PROPERTY_TYPE, Registration :: TYPE_APPLICATION);
        $condition = new AndCondition($conditions);

        $registrations = AdminDataManager :: get_instance()->retrieve_registrations($condition, array(), 0, 1);

        if ($registrations->size() === 0)
        {
            $message .= '--' . Translation :: get('Nothing') . '--';
            $this->get_message_logger()->add_message($message);
            return false;
        }
        else
        {
            $registration = $registrations->next_result();

            $application_version = $this->version_compare($version['type'], $version['_content'], $registration->get_version());
            if (! $application_version)
            {
                $message .= '--' . Translation :: get('WrongVersion') . '--';
                $this->get_message_logger()->add_message($message);
                $this->get_message_logger()->add_message(Translation :: get('DependencyApplicationWrongVersion'), MessageLogger :: TYPE_WARNING);
                return false;
            }
            else
            {
                if (! $registration->is_active())
                {
                    $message .= '--' . Translation :: get('InactiveApplication') . '--';
                    $this->get_message_logger()->add_message($message);
                    $this->get_message_logger()->add_message(Translation :: get('DependencyActivateObjectWarning'), MessageLogger :: TYPE_WARNING);
                }
                else
                {
                    $message .= $registration->get_version();
                    $this->get_message_logger()->add_message($message);
                }

                return true;
            }
        }
    }
}
?>
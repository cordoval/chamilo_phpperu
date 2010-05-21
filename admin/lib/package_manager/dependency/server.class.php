<?php
/**
 * $Id: server.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.dependency
 */

class ServerPackageDependency extends PackageDependency
{
    const PROPERTY_VERSION = 'version';

    private $version;

    function ServerPackageDependency($dependency)
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

    function check()
    {
        $version = $this->get_version();
        $message = Translation :: get('DependencyCheckServer') . ': ' . $this->as_html() . ' ' . Translation :: get('Found') . ': ';

        switch ($this->get_id())
        {
            case 'php' :
                $message .= phpversion();
                $this->get_message_logger()->add_message($message);
                return $this->version_compare($version['type'], $version['_content'], phpversion());
                break;
            default :
                return true;
        }
    }

    function as_html()
    {
        $version = $this->get_version();
        return $this->get_id() . '. ' . Translation :: get('Expecting') . ': ' . $version['_content'];
    }
}
?>
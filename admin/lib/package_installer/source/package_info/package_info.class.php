<?php
class PackageInfo
{
	private $package_name;

	function PackageInfo($package_name)
	{
		$this->package_name = $package_name;

	}

    static function factory($type, $package_name)
    {
        $class = Utilities :: underscores_to_camelcase($type) . 'PackageInfo';
        require_once dirname(__FILE__) . '/type/' . $type . '.class.php';
        return new $class($package_name);
    }

    function get_package_info()
    {
    	$path = $this->get_path();
    	$file = $path . 'package.info';

    	if (file_exists($file))
    	{
        	$xml_data = file_get_contents($file);
        	if ($xml_data)
            {
                $unserializer = new XML_Unserializer();
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('dependency'));

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
    	else
    	{
    	    return false;
    	}
    }

    function get_package_name()
    {
    	return $this->package_name;
    }

	function get_package()
	{
        $package_info = $this->get_package_info();
        $package_info['package']['dependencies'] = serialize($package_info['package']['dependencies']);

		$package = new RemotePackage();
		$package->set_default_properties($package_info['package']);
        return $package;
	}
}
?>

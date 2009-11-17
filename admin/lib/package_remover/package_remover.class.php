<?php

/**
 * $Id: package_remover.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_remover
 */
abstract class PackageRemover
{
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';
    
    private $parent;
    private $package;
    private $message;
    private $html;

    function PackageRemover($package_manager)
    {
        $this->parent = $package_manager;
        $this->package = Request :: get(PackageManager :: PARAM_PACKAGE);
        $this->message = array();
        $this->html = array();
    }

    function get_parent()
    {
        return $this->parent;
    }

    function section($parent)
    {
        $this->parent = $parent;
    }

    function get_package()
    {
        return $this->package;
    }

    function set_package($package)
    {
        $this->package = $package;
    }

    abstract function run();

    function check_dependencies()
    {
        $adm = AdminDataManager :: get_instance();
        $package = $adm->retrieve_registration($this->get_package());
        
        $condition = new NotCondition(new EqualityCondition(Registration :: PROPERTY_ID, $this->get_package()));
        $registrations = $adm->retrieve_registrations($condition);
        
        $failures = 0;
        
        while ($registration = $registrations->next_result())
        {
            $type = $registration->get_type();
            
            switch ($type)
            {
                case Registration :: TYPE_APPLICATION :
                    $info_path = Path :: get_application_path() . 'lib/' . $registration->get_name() . '/package.info';
                    break;
                case Registration :: TYPE_CONTENT_OBJECT :
                    $info_path = Path :: get_repository_path() . 'lib/content_object/' . $registration->get_name() . '/package.info';
                    break;
            }
            
            $package_data = $this->get_package_info($info_path);
            
            if ($package_data)
            {
                if (! $this->parse_packages_info($package, $package_data))
                {
                    $failures ++;
                }
            }
        }
        
        if ($failures > 0)
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Invokes the constructor of the class that corresponds to the specified
     * type of package remover.
     */
    static function factory($type, $parent)
    {
        $class = 'Package' . Utilities :: underscores_to_camelcase($type) . 'Remover';
        require_once dirname(__FILE__) . '/type/' . $type . '.class.php';
        return new $class($parent);
    }

    function add_message($message, $type = self :: TYPE_NORMAL)
    {
        switch ($type)
        {
            case self :: TYPE_NORMAL :
                $this->message[] = $message;
                break;
            case self :: TYPE_CONFIRM :
                $this->message[] = '<span style="color: green; font-weight: bold;">' . $message . '</span>';
                break;
            case self :: TYPE_WARNING :
                $this->message[] = '<span style="color: orange; font-weight: bold;">' . $message . '</span>';
                break;
            case self :: TYPE_ERROR :
                $this->message[] = '<span style="color: red; font-weight: bold;">' . $message . '</span>';
                break;
            default :
                $this->message[] = $message;
                break;
        }
    }

    function set_message($message)
    {
        $this->message = $message;
    }

    function get_message()
    {
        return $this->message;
    }

    function set_html($html)
    {
        $this->html = $html;
    }

    function get_html()
    {
        return $this->html;
    }

    function retrieve_message()
    {
        $message = implode('<br />' . "\n", $this->get_message());
        $this->set_message(array());
        return $message;
    }

    function installation_failed($type, $error_message = null)
    {
        if ($error_message)
        {
            $this->add_message($error_message, self :: TYPE_ERROR);
        }
        $this->add_message(Translation :: get('PackageRemovalFailed'), self :: TYPE_ERROR);
        $this->process_result($type);
        return false;
    }

    function installation_successful($type, $message = null)
    {
        if ($message)
        {
            $this->add_message($message, self :: TYPE_CONFIRM);
        }
        $this->process_result($type);
        return true;
    }

    function add_html($html)
    {
        $this->html[] = $html;
    }

    function process_result($type = '')
    {
        $this->add_html('<div class="content_object" style="padding: 15px 15px 15px 76px; background-image: url(' . Theme :: get_image_path() . 'place_' . $type . '.png);">');
        //		$this->add_html('<div class="content_object">');
        $this->add_html('<div class="title">' . Translation :: get(Utilities :: underscores_to_camelcase($type)) . '</div>');
        $this->add_html('<div class="description">');
        $this->add_html($this->retrieve_message());
        $this->add_html('</div>');
        $this->add_html('</div>');
    }

    function retrieve_result()
    {
        return implode("\n", $this->get_html());
    }

    function get_package_info($info_path)
    {
        $xml_data = file_get_contents($info_path);
        
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
                exit();
            }
            else
            {
                return $unserializer->getUnserializedData();
            }
        }
        else
        {
            return null;
        }
    }

    function parse_packages_info($registration, $data)
    {
        $type = $registration->get_type();
        
        switch ($type)
        {
            case Registration :: TYPE_APPLICATION :
                $dependency_type = 'applications';
                break;
            case Registration :: TYPE_CONTENT_OBJECT :
                $dependency_type = 'content_objects';
                break;
        }
        
        foreach ($data['package'] as $package)
        {
            $dependencies = $package['dependencies'];
            
            if (isset($dependencies[$dependency_type]))
            {
                foreach ($dependencies[$dependency_type]['dependency'] as $dependency)
                {
                    if ($dependency['id'] === $registration->get_name())
                    {
                        $message = Translation :: get('PackageDependency') . ': <em>' . $package['name'] . ' (' . $package['code'] . ')</em>';
                        $this->add_message($message);
                        return false;
                    }
                }
            }
        }
        
        return true;
    }
}
?>
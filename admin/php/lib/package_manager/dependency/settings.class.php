<?php

/**
 * $Id: settings.class.php 126 2009-11-09 13:11:05Z vanpouckesven $
 * @package admin.lib.package_installer.dependency
 */

class SettingsPackageDependency extends PackageDependency
{
    const PROPERTY_VALUE = 'value';

    private $value;

    function SettingsPackageDependency($dependency)
    {
        parent :: __construct($dependency);
        $this->set_value($dependency['value']);
    }

    /**
     * @return the $value
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * @param $value the $value to set
     */
    public function set_value($value)
    {
        $this->value = $value;
    }

    function check()
    {
        $setting = ini_get($this->get_id());
        $message = Translation :: get('DependencyCheckSetting') . ': ' . $this->as_html() . ' ' . Translation :: get('Found') . ': ' . $setting;
        $value = $this->get_value();
        $this->logger->add_message($message);
        return $this->compare($value['type'], $value['_content'], $setting);
    }

    function as_html()
    {
        $value = $this->get_value();
        return $this->get_id() . '. ' . Translation :: get('Expecting') . ': ' . $value['_content'];
    }
}
?>
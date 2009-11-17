<?php
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_source.class.php';
require_once Path :: get_admin_path() . 'lib/package_installer/package_installer_type.class.php';
/**
 * $Id: package_installer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.package_installer
 */
class PackageInstaller
{
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';
    
    private $source;
    private $message;
    private $html;

    function PackageInstaller()
    {
        $this->source = Request :: get(PackageManager :: PARAM_INSTALL_TYPE);
        $this->message = array();
        $this->html = array();
    }

    function run()
    {
        $installer_source = PackageInstallerSource :: factory($this, $this->source);
        if (! $installer_source->process())
        {
            return $this->installation_failed('source', Translation :: get('PackageRetrievalFailed'));
        }
        else
        {
            $this->process_result('Source');
            
            $attributes = $installer_source->get_attributes();
            $package = PackageInstallerType :: factory($this, $attributes->get_section(), $installer_source);
            if (! $package->install())
            {
                return $this->installation_failed('settings', Translation :: get('PackageProcessingFailed'));
            }
            else
            {
                $this->installation_successful('settings', Translation :: get('ApplicationSettingsDone'));
                return $this->installation_successful('finished', Translation :: get('PackageCompletelyInstalled'));
            }
        }
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
        $this->add_message(Translation :: get('PackageInstallFailed'), self :: TYPE_ERROR);
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
}
?>
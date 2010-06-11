<?php
require_once Path :: get_admin_path() . 'lib/package_manager/package_dependency_verifier.class.php';
require_once Path :: get_admin_path() . 'lib/package_installer/source/package_info/package_info.class.php';

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
        $registration = AdminDataManager :: get_instance()->retrieve_registration($this->get_package());
        $package_info = PackageInfo :: factory($registration->get_type(), $registration->get_name());
        $package = $package_info->get_package();

        $verifier = new PackageDependencyVerifier($package);
        $success = $verifier->is_removable();
        $this->add_message($verifier->get_logger()->render());
        if (! $success)
        {
            return false;
        }

        return true;
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
}
?>
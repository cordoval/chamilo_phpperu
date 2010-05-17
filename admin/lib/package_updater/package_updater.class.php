<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_source.class.php';
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_type.class.php';

class PackageUpdater
{
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';

    private $source;
    private $message;
    private $html;

    function PackageUpdater()
    {
    	$this->source = Request :: get(PackageManager :: PARAM_INSTALL_TYPE);
        $this->message = array();
        $this->html = array();
    }

    function run()
    {
    	$updater_source = PackageUpdaterSource :: factory($this, $this->source);
        if (! $updater_source->process())
        {
            return $this->update_failed('source', Translation :: get('PackageRetrievalFailed'));
        }
        else
        {
            $is_registered = AdminDataManager :: is_registered($updater_source->get_attributes()->get_name(), $updater_source->get_attributes()->get_section());
            if($is_registered)
            {
           		return $this->update_failed('source', Translation :: get('PackageIsAlreadyRegistered'));
            }

        	$this->process_result('Source');

            $attributes = $updater_source->get_attributes();
            $package = PackageUpdaterType :: factory($this, $attributes->get_section(), $updater_source);
            if (! $package->update())
            {
                return $this->update_failed('settings', Translation :: get('PackageProcessingFailed'));
            }
            else
            {
                $this->update_successful('settings', Translation :: get('ApplicationSettingsDone'));
                return $this->update_successful('finished', Translation :: get('PackageCompletelyUpdated'));
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

    function update_failed($type, $error_message = null)
    {
        if ($error_message)
        {
            $this->add_message($error_message, self :: TYPE_ERROR);
        }
        $this->add_message(Translation :: get('PackageUpdateFailed'), self :: TYPE_ERROR);
        $this->process_result($type);
        return false;
    }

    function update_successful($type, $message = null)
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
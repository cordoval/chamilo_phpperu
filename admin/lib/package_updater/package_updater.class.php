<?php
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_source.class.php';
require_once Path :: get_admin_path() . 'lib/package_updater/package_updater_type.class.php';
require_once Path :: get_common_path() . 'database/backup/database_backup.class.php';

class PackageUpdater
{
    const TYPE_NORMAL = '1';
    const TYPE_CONFIRM = '2';
    const TYPE_WARNING = '3';
    const TYPE_ERROR = '4';

    private $message;
    private $html;
    private $registration;
    private $source;

    /**
     * @return the $registration
     */
    public function get_registration()
    {
        return $this->registration;
    }

	/**
     * @param $registration the $registration to set
     */
    public function set_registration($registration)
    {
        $this->registration = $registration;
    }

	function PackageUpdater()
    {
    	$this->registration = AdminDataManager::get_instance()->retrieve_registration(Request :: get(PackageManager::PARAM_REGISTRATION));
        $this->message = array();
        $this->html = array();
        $this->source = Request :: get(PackageManager :: PARAM_INSTALL_TYPE);
    }

    function deactivate_package()
    {
    	$this->registration->deactivate();
    	return $this->registration->update();
    }
    
	function activate_package()
    {
    	$this->registration->activate();
    	return $this->registration->update();
    }
    
    function get_remote_package()
    {
    	$id = Request :: get(PackageManager::PARAM_REGISTRATION);
		$registration = AdminDataManager::get_instance()->retrieve_registration($id);
      
        $conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_CODE, $registration->get_name());
        $conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_SECTION, $registration->get_type());
        $condition = new AndCondition($conditions);
        
        $admin = AdminDataManager::get_instance();
        $order_by = new ObjectTableOrder(RemotePackage :: PROPERTY_VERSION, SORT_DESC);
        
        $package_remote = $admin->retrieve_remote_packages($condition, $order_by, null, 1);
        if ($package_remote->size() == 1)
        {
        	return $package_remote->next_result();
        }
        else
        {
        	return false;
        }
    }
    
	function verify_dependencies()
    {
		$registration = $this->get_registration();
    	
		$conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_CODE, $registration->get_name());
        $conditions[] = new EqualityCondition(RemotePackage :: PROPERTY_SECTION, $registration->get_type());
        $condition = new AndCondition($conditions);
        
        $admin = AdminDataManager::get_instance();
        $order_by = new ObjectTableOrder(RemotePackage :: PROPERTY_VERSION, SORT_DESC);
        
        $package_remote = $admin->retrieve_remote_packages($condition, $order_by, null, 1);
        if ($package_remote->size() == 1)
        {
        	$package_remote = $package_remote->next_result();

	        $package_update_dependency = new PackageDependencyVerifier($package_remote);
	        $success_update = $package_update_dependency->is_updatable();
			$this->add_message($package_update_dependency->get_message_logger()->render());
			
			if (! $success_update)
			{
				return $this->update_failed('reliabilities', Translation :: get('ReliabilitiesFailed'));
			}
			else
			{
				$this->process_result('Reliabilities');
			}

	        $success_install = $package_update_dependency->is_installable();
	        $this->add_message($package_update_dependency->get_message_logger()->render());
			if (! $success_install)
			{
				return $this->update_failed('dependencies', Translation :: get('DependenciesFailed'));
			}
        	else
			{
				$this->process_result('Dependencies');
			}
        }
        return true;
    }
    
    function run()
    {
		if ($this->deactivate_package())
		{
			$this->add_message(Translation :: get('PackageDeactivated'), self :: TYPE_CONFIRM);
			$this->process_result('Status');
		}
		else
		{
			return $this->update_failed('status', Translation :: get('PackageDeactivationFailed'));
		}
		
		if (! $this->verify_dependencies())
		{
			return false;			
		}
    			
    	$updater_source = PackageUpdaterSource :: factory($this, $this->source);
        if (! $updater_source->process())
        {
        	return $this->update_failed('source', Translation :: get('PackageRetrieveFailed'));
        }
        else
        {      	
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
                
            }
        }
        
    	if ($this->activate_package())
		{
			$this->add_message(Translation :: get('PackageActivated'), self :: TYPE_CONFIRM);
			$this->process_result('Status');
			return $this->update_successful('finished', Translation :: get('PackageCompletelyUpdated'));
		}
		else
		{
			return $this->update_failed('status', Translation :: get('PackageActivationFailed'));
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
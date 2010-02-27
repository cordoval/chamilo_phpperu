<?php
/**
 * $Id: requirements_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/install_wizard_page.class.php';
require_once Path :: get_library_path() . 'diagnoser/diagnoser.class.php';
/**
 * Class for requirements page
 * This checks and informs about some requirements for installing Chamilo:
 * - necessary and optional extensions
 * - folders which have to be writable
 */
class RequirementsInstallWizardPage extends InstallWizardPage
{

    function get_title()
    {
        return Translation :: get("Requirements");
    }

    /**
     * this function checks if a php extension exists or not
     *
     * @param string  $extentionName  name of the php extension to be checked
     * @param boolean  $echoWhenOk  true => show ok when the extension exists
     * @author Christophe Gesche
     */
    /*function check_extension($extentionName)
	{
		if (extension_loaded($extentionName))
		{
			return '<li>'.$extentionName.' - ok</li>';
		}
		else
		{
			return '<li><b>'.$extentionName.'</b> <font color="red">is missing (Chamilo can work without)</font> (<a href="http://www.php.net/'.$extentionName.'" target="_blank">'.$extentionName.'</a>)</li>';
		}
	}

	function get_not_writable_folders()
	{
		$writable_folders = array ('../files','../home','../common/configuration');
		$not_writable = array ();
		foreach ($writable_folders as $index => $folder)
		{
			if (!is_writable($folder) && !@ chmod($folder, 0777))
			{
				$not_writable[] = $folder;
			}
		}
		return $not_writable;
	}

	function get_info()
	{
		$not_writable = $this->get_not_writable_folders();

		if (count($not_writable) > 0)
		{
			$info[] = '<div style="margin:20px;padding:10px;width: 50%;color:#FF6600;border:2px solid #FF6600;">';
			$info[] = 'Some files or folders don\'t have writing permission. To be able to install Chamilo you should first change their permissions (using CHMOD). Please read the <a href="../../documentation/installation_guide.html" target="blank">installation guide</a>.';
			$info[] = '<ul>';
			foreach ($not_writable as $index => $folder)
			{
				$info[] = '<li>'.$folder.'</li>';
			}
			$info[] = '</ul>';
			$info[] = '</div>';
			$this->disableNext = true;
		}
		elseif (file_exists('../inc/conf/claro_main.conf.php'))
		{
			$info[] = '<div style="margin:20px;padding:10px;width: 50%;color:#FF6600;border:2px solid #FF6600;text-align:center;">';
			$info[] = Translation :: get("WarningExistingChamiloInstallationDetected");
			$info[] = '</div>';
		}
		$info[] = '<br /><b>'.Translation :: get("ReadThoroughly").'</b><br />';
		$info[] = '<br />';
		$info[] = Translation :: get("ChamiloNeedFollowingOnServer");
		$info[] = "<ul>";
		$info[] = "<li>Webserver with PHP 5.x";
		$info[] = '<ul>';
		$info[] = $this->check_extension('standard');
		$info[] = $this->check_extension('session');
		$info[] = $this->check_extension('mysql');
		$info[] = $this->check_extension('zlib');
		$info[] = $this->check_extension('pcre');
		$info[] = $this->check_extension('xsl');
		$info[] = '</ul></li>';
		$info[] = "<li>MySQL + login/password allowing to access and create at least one database</li>";
		$info[] = "<li>Write access to web directory where Chamilo files have been put</li>";
		$info[] = "</ul>";
		$info[] = Translation :: get('MoreDetails').", <a href=\"../../documentation/installation_guide.html\" target=\"blank\">read the installation guide</a>.";
		return implode("\n",$info);
	}*/

    private $fatal = false;

    function get_info()
    {
//        $table = new SimpleTable($this->get_data(), new DiagnoserCellRenderer(), null, 'diagnoser');

//        $info[] = '<br />';
//        $info[] = '<b>' . Translation :: get("ReadThoroughly") . '</b>';
//        $info[] = '<br /><br />';
        $info[] = Translation :: get("ChamiloNeedFollowingOnServer");
//        $info[] = '<br /><br />';
//        $info[] = $table->toHTML();
        $info[] = '<br />';
        $info[] = Translation :: get('MoreDetails') . ", <a href=\"../../documentation/installation_guide.html\" target=\"blank\">read the installation guide</a>.";
        $info[] = '<br /><br />';
        $info[] = '<b>' . Translation :: get("ReadThoroughly") . '</b>';
        $info[] = '<br />';

        return implode("\n", $info);
    }

    function get_data()
    {
        $array = array();
        $diagnoser = new Diagnoser();

        $urlAppendPath = str_replace('/install/index.php', '', $_SERVER['PHP_SELF']);
        $path = 'http://' . $_SERVER['HTTP_HOST'] . $urlAppendPath . '/';

        $path .= '/layout/aqua/images/common/';

        /*$writable_folders = array('/files', '/files/archive', '/files/fckeditor', '/files/garbage', '/files/logs'
        						  , '/files/repository/', '/files/temp', '/files/scorm', '/files/userpictures',
        						  '/home', '/common/configuration');*/

        $files_folder = Path :: get(SYS_PATH) . '/files';

        if (!file_exists($files_folder))
        {
           	mkdir($files_folder);
        }

        $writable_folders = array('/files', '/common/configuration');

        foreach ($writable_folders as $folder)
        {
            $exists   = file_exists(Path :: get(SYS_PATH) . $folder);
            $writable = is_writable(Path :: get(SYS_PATH) . $folder);

            if (! $exists || ! $writable)
            {
                $this->fatal = true;
            }

            $status = $exists && $writable ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;
            $array[] = $diagnoser->build_setting($status, '[FILES]', Translation :: get($exists ? 'IsWritable' : 'DirectoryExists') . ': ' . $folder, $exists ? 'http://php.net/manual/en/function.is-writable.php' : 'http://php.net/manual/en/function.file-exists.php', $writable, 1, 'yes_no', Translation :: get($exists ? 'DirectoryMustBeWritable' : 'DirectoryMustExist'), $path);
        }

        $version = phpversion();
        $status = $version > '5.2' ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;
        if($status == Diagnoser :: STATUS_ERROR)
        	$this->fatal = true;
        $array[] = $diagnoser->build_setting($status, '[PHP]', 'phpversion()', 'http://www.php.net/manual/en/function.phpversion.php', phpversion(), '>= 5.2', null, Translation :: get('PHPVersionInfo'), $path);

//        $setting = ini_get('output_buffering');
//	    $req_setting = 1;
//	    $status = $setting == $req_setting ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;
//	    if($status == Diagnoser :: STATUS_ERROR)
//        	$this->fatal = true;
//        $array[] = $diagnoser->build_setting($status, '[PHP-INI]', 'output_buffering', 'http://www.php.net/manual/en/outcontrol.configuration.php#ini.output-buffering', $setting, $req_setting, 'on_off', Translation :: get('FileUploadsInfo'), $path);

        $extensions = array('gd' => 'http://www.php.net/gd', 'pcre' => 'http://www.php.net/pcre', 'session' => 'http://www.php.net/session', 'standard' => 'http://www.php.net/spl', 'zlib' => 'http://www.php.net/zlib', 'xsl' => 'http://be2.php.net/xsl');

        foreach ($extensions as $extension => $url)
        {
            $loaded = extension_loaded($extension);

            if (! $loaded)
            {
                $this->fatal = true;
            }

            $status = $loaded ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;
            $array[] = $diagnoser->build_setting($status, '[PHP-EXTENSION]', Translation :: get('ExtensionLoaded') . ': ' . $extension, $url, $loaded, 1, 'yes_no', Translation :: get('ExtensionMustBeLoaded'), $path);
        }

        return $array;
    }

    function buildForm()
    {
        Session :: register('normal_install', 1);

    	$this->set_lang($this->controller->exportValue('page_language', 'install_language'));

        $this->_formBuilt = true;

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Previous'), array('class' => 'normal previous'));
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));

        $table = new SimpleTable($this->get_data(), new DiagnoserCellRenderer(), null, 'diagnoser');
        $this->addElement('html', $table->toHTML());


        $this->get_data();
        if ($this->fatal)
        {
            $el = $buttons[1];
            $el->updateAttributes('disabled="disabled"');
        }
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
        $this->set_form_defaults();
    }

    function set_form_defaults()
    {
        $defaults = array();
        $defaults['installation_type'] = 'new';
        $this->setDefaults($defaults);
    }
}
?>
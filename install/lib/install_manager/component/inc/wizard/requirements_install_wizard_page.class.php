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

    private $fatal = false;

    function get_info()
    {
        $info[] = Translation :: get("ChamiloNeedFollowingOnServer");
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
        {
        	$this->fatal = true;
        }
        $array[] = $diagnoser->build_setting($status, '[PHP]', 'phpversion()', 'http://www.php.net/manual/en/function.phpversion.php', phpversion(), '>= 5.2', null, Translation :: get('PHPVersionInfo'), $path);

		$setting = ini_get('output_buffering');
	    $req_setting = 0;
	    $status = $setting == $req_setting ? Diagnoser :: STATUS_OK : Diagnoser :: STATUS_ERROR;
    	if($status == Diagnoser :: STATUS_ERROR)
        {
        	$this->fatal = true;
        }
        
        $array[] = $diagnoser->build_setting($status, '[PHP-INI]', 'output_buffering', 'http://www.php.net/manual/en/outcontrol.configuration.php#ini.output-buffering', $setting, $req_setting, 'on_off', Translation :: get('FileUploadsInfo'), $path);

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
    	$this->set_lang($this->controller->exportValue('page_language', 'install_language'));

        $this->_formBuilt = true;

        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Previous'), array('class' => 'normal previous'));
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('refresh'), Translation :: get('Refresh'), array('class' => 'normal refresh', 'id' => 'refresh_button'));
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));

        $table = new SimpleTable($this->get_data(), new DiagnoserCellRenderer(), null, 'diagnoser');
        $this->addElement('html', $table->toHTML());

        $script = array();
        $script[] = '<script type="text/javascript">';
        $script[] = '//Tim brouckaert 2010 03 11: added for refresh button';
        $script[] = '$(document).ready(function ()';
        $script[] = '{';
        $script[] = '	$(\'#refresh_button\').click(function(){';
        $script[] = '		location.reload();';
        $script[] = '		return false;';
        $script[] = '	});';
        $script[] = '});';
        $script[] = '</script>';

        $this->addElement('html', implode("\n", $script));

        $this->get_data();
        if ($this->fatal)
        {
            $el = $buttons[2];
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
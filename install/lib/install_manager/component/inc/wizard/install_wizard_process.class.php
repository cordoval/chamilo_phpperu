<?php
/**
 * $Id: install_wizard_process.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component.inc.wizard
 */


/**
 * This class implements the action to take after the user has completed a
 * course maintenance wizard
 */
class InstallWizardProcess extends HTML_QuickForm_Action
{
    /**
     * The repository tool in which the wizard runs.
     */
    private $parent;

    private $applications = array();
    private $values;

    private $counter = 0;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs.
     */
    public function InstallWizardProcess($parent)
    {
        $this->parent = $parent;
    }

    function perform($page, $actionName)
    {
        if(array_key_exists('config_file', $_FILES))
        {
        	$values = array();

        	require_once($_FILES['config_file']['tmp_name']);
        	$this->values = $values;
        }
        else
        {
    		$this->values = $page->controller->exportValues();
        }

        $this->applications['core'] = array('webservice', 'admin', 'help', 'reporting', 'tracking', 'repository', 'user', 'group', 'rights', 'home', 'menu', 'migration');
        $this->applications['extra'] = Filesystem :: get_directory_content(Path :: get_application_path() . 'lib/', Filesystem :: LIST_DIRECTORIES, false);

        $this->display_header($page);

        // Display the page header
        echo '<h3>' . Translation :: get('PreProduction') . '</h3>';

        // 1. Connection to the DBMS and create the database
        $db_creation = $this->create_database();
        $this->process_result('database', $db_creation['success'], $db_creation['message']);
        flush();

        // 2. Write the config files
        $config_file = $this->write_config_file();
        $this->process_result('config', $config_file['success'], $config_file['message']);
        flush();

        //Sets the correct database before installing applications
        $connection = Connection :: get_instance();
        $connection->get_connection()->setDatabase($this->values['database_name']);
        
        $this->counter ++;

        // 3. Installing the applications
        echo '<h3>' . Translation :: get('Applications') . '</h3>';
        $this->install_applications();

        $this->counter ++;

        // 4. Post-Processing all applications
        $this->post_process();

        $this->counter ++;

        echo '<h3>' . Translation :: get('Filesystem') . '</h3>';
        // 5. Create additional folders
        $folder_creation = $this->create_folders();
        $this->process_result('folder', $folder_creation['success'], $folder_creation['message']);
        flush();

        $this->counter ++;

        echo '<h3>' . Translation :: get('Finished') . '</h3>';

        // 6. If all goes well we now show the link to the portal
        $message = '<a href="../index.php">' . Translation :: get('GoToYourNewlyCreatedPortal') . '</a>';
        $this->process_result('finished', true, $message, false);
        flush();

//        $page->controller->container(true);

		//echo '<a href="#" id="showall">' . Translation :: get('ShowAll') . '</a> - <a href="#" id="hideall">' . Translation :: get('HideAll') . '</a>';

        // Display the page footer
        $this->parent->display_footer();
    }

    function display_header($page)
    {
        $values = $this->values;

    	$this->parent->display_header(array(), "install");
    	$all_pages = $page->controller->_pages;

        $total_number_of_pages = count($all_pages) + 1;

        $html[] = '<div id="progressbox">';
        $html[] = '<ul id="progresstrail">';

        $page_number = 1;

        foreach($all_pages as $page)
        {
        	 $html[] = '<li class="active"><a href="#">' . $page_number . '.&nbsp;&nbsp;' . $page->get_title() . '</a></li>';
        	 $page_number++;
        }

        $html[] = '<li class="active"><a href="#">' . $total_number_of_pages . '.&nbsp;&nbsp;' . Translation :: get('Installation') . '</a></li>';
        $html[] = '</ul>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';

        $html[] = '<div id="theForm" style="margin: 10px;">';
        $html[] = '<div id="select" class="row"><div class="formc formc_no_margin">';
        $html[] = '<b>' . Translation :: get('Step') . ' ' . $total_number_of_pages . ' ' . Translation :: get('of') . ' ' . $total_number_of_pages . ' &ndash; ' . Translation :: get('Installation') . '</b><br />';

        $html[] = Translation :: get('InstallationDescription');
        $html[] = '</div>';
        $html[] = '</div></div>';
        $html[] = '<div class="clear"></div>';
        //$html[] = '<a href="#" id="showall">' . Translation :: get('ShowAll') . '</a> - <a href="#" id="hideall">' . Translation :: get('HideAll') . '</a><br />';
        //$html[] = ResourceManager :: get_instance()->get_resource_html($values['platform_url'] . 'common/javascript/install_process.js');

        echo implode("\n", $html);
    }

    function create_database()
    {
        $values = $this->values;

        $connection_string = $values['database_driver'] . '://' . $values['database_username'] . ':' . $values['database_password'] . '@' . $values['database_host'];
        $connection = MDB2 :: connect($connection_string);

        if (MDB2 :: isError($connection))
        {
            return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => (Translation :: get('DBConnectError') . $connection->getMessage()));
        }
        else
        {
        	$connection->loadModule('Manager');
            $database_exists = $connection->databaseExists($values['database_name']);
            
            if ($database_exists == true)
            {
                if(array_key_exists('database_exists', $values) && $values['database_exists'] == 1)
           		{
            		return array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('UseExistingDB'));
           		}
           		
            	$drop_result = $connection->dropDatabase($values['database_name']);

                if (MDB2 :: isError($drop_result))
                {
                    return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => (Translation :: get('DBDropError') . ' ' . $drop_result->getMessage()));
                }
            }

            $create_result = $connection->createDatabase($values['database_name'], array('charset' => 'utf8', 'collation' => 'utf8_unicode_ci'));

            if (! MDB2 :: isError($create_result))
            {
                return array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('DBCreated'));
            }
            else
            {
                return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => (Translation :: get('DBCreateError') . ' ' . $create_result->getMessage()));
            }
        }
    }

    function create_folders()
    {
        $files_path = dirname(__FILE__) . '/../../../../../../files/';
        $directories = array('archive', 'fckeditor', 'garbage', 'repository', 'temp', 'userpictures', 'scorm', 'logs', 'hotpotatoes');
        foreach ($directories as $directory)
        {
            $path = $files_path . $directory;

            if (file_exists($path) && is_dir($path))
                Filesystem :: remove($path);

            if (! Filesystem :: create_dir($path))
            {
                return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('FoldersCreatedFailed'));
            }
        }
        return array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('FoldersCreatedSuccess'));
    }

    function write_config_file()
    {
        $values = $this->values;

        $content = file_get_contents('../common/configuration/configuration.dist.php');

        if ($content === false)
        {
            return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('ConfigWriteFailed'));
        }

        $config['{DATABASE_DRIVER}'] = $values['database_driver'];
        $config['{DATABASE_HOST}'] = $values['database_host'];
        $config['{DATABASE_USER}'] = $values['database_username'];
        $config['{DATABASE_PASSWORD}'] = $values['database_password'];
        $config['{DATABASE_NAME}'] = $values['database_name'];
        $config['{ROOT_WEB}'] = $values['platform_url'];
        $config['{ROOT_SYS}'] = str_replace('\\', '/', realpath($values['platform_url']) . '/');
        $config['{SECURITY_KEY}'] = md5(uniqid(rand() . time()));
        $config['{URL_APPEND}'] = str_replace('/install/index.php', '', $_SERVER['PHP_SELF']);
        $config['{HASHING_ALGORITHM}'] = $values['hashing_algorithm'];
        $config['{INSTALL_DATE}'] = time();

        foreach ($config as $key => $value)
        {
            $content = str_replace($key, $value, $content);
        }

        $fp = fopen('../common/configuration/configuration.php', 'w');

        if ($fp !== false)
        {

            if (fwrite($fp, $content))
            {
                fclose($fp);
                return array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('ConfigWriteSuccess'));
            }
            else
            {
                return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('ConfigWriteFailed'));
            }
        }
        else
        {
            return array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('ConfigWriteFailed'));
        }
    }

    function install_applications()
    {

        $core_applications = $this->applications['core'];
        $applications = $this->applications['extra'];
        $values = $this->values;

        foreach ($core_applications as $core_application)
        {

            $installer = Installer :: factory($core_application, $values);
            $result = $installer->install();
            $this->process_result($core_application, $result, $installer->retrieve_message());
            unset($installer);
            flush();

        }

        flush();

        foreach ($applications as $application)
        {
            $toolPath = Path :: get_application_path() . 'lib/' . $application . '/install';
            if (is_dir($toolPath) && WebApplication :: is_application_name($application))
            {
                $check_name = 'install_' . $application;
                if (isset($values[$check_name]) && $values[$check_name] == '1')
                {
                    $installer = Installer :: factory($application, $values);
                    $result = $installer->install();
                    $this->process_result($application, $result, $installer->retrieve_message());
                    unset($installer, $result);
                    flush();
                }
                //				else
            //				{
            //					// TODO: Does this work ?
            //					$application_path = dirname(__FILE__).'/../../application/lib/' . $application . '/';
            //					if (!Filesystem::remove($application_path))
            //					{
            //						$this->process_result($application, array(Installer :: INSTALL_SUCCESS => false, Installer :: INSTALL_MESSAGE => Translation :: get('ApplicationRemoveFailed')));
            //					}
            //					else
            //					{
            //						$this->process_result($application, array(Installer :: INSTALL_SUCCESS => true, Installer :: INSTALL_MESSAGE => Translation :: get('ApplicationRemoveSuccess')));
            //					}
            //				}
            }
            flush();
        }
    }

    function register_reporting()
    {
        $core_applications = $this->applications['core'];
        $applications = $this->applications['extra'];
        $values = $this->values;

        foreach ($core_applications as $core_application)
        {
            $installer = Installer :: factory($core_application, $values);
            $result = $installer->register_reporting();

            $this->process_result($core_application, $result, $installer->retrieve_message());

            unset($installer);
            flush();
        }

        foreach ($applications as $application)
        {
            $toolPath = Path :: get_application_path() . 'lib/' . $application . '/install';
            if (is_dir($toolPath) && WebApplication :: is_application_name($application))
            {
                $check_name = 'install_' . $application;
                if (isset($values[$check_name]) && $values[$check_name] == '1')
                {
                    $installer = Installer :: factory($application, $values);
                    $result = $installer->register_reporting();
                    $this->process_result($application, $result, $installer->retrieve_message());

                    unset($installer, $result);
                    flush();
                }
            }
            flush();
        }
    } //register_reporting


    function register_trackers()
    {
        $core_applications = $this->applications['core'];
        $applications = $this->applications['extra'];
        $values = $this->values;

        // Roles'n'rights for core applications
        foreach ($core_applications as $core_application)
        {
            $installer = Installer :: factory($core_application, $values);
            $result = $installer->register_trackers();

            $this->process_result($core_application, $result, $installer->retrieve_message());

            unset($installer);
            flush();
        }

        // Roles'n'rights for selected applications
        foreach ($applications as $application)
        {
            $toolPath = Path :: get_application_path() . 'lib/' . $application . '/install';
            if (is_dir($toolPath) && WebApplication :: is_application_name($application))
            {
                $check_name = 'install_' . $application;
                if (isset($values[$check_name]) && $values[$check_name] == '1')
                {
                    $installer = Installer :: factory($application, $values);
                    $result = $installer->register_trackers();
                    $this->process_result($application, $result, $installer->retrieve_message());

                    unset($installer, $result);
                    flush();
                }
            }
            flush();
        }
    }

    function post_process()
    {
        // Post processing includes a.o.:
        // 1. Roles and rights
        // 2. Tracking
        // 3. Reporting
        // 4. "Various"
        // Check the installer class for a comprehensive list.
        // Class located at: ./common/installer.class.php


        $core_applications = $this->applications['core'];
        $applications = $this->applications['extra'];
        $values = $this->values;

        // Post-processing for core applications
        echo '<h3>' . Translation :: get('PostProcessingCoreApplications') . '</h3>';
        foreach ($core_applications as $core_application)
        {
            $installer = Installer :: factory($core_application, $values);
            $result = $installer->post_process();

            $this->process_result($core_application, $result, $installer->retrieve_message());

            unset($installer);
            flush();
        }
        
        //installation of contentObject
        echo '<h3>' . Translation :: get('InstallingContentObjects') . '</h3>';
        $dir = Path :: get_repository_path() . 'lib/content_object';
        // Register the learning objects
        $folders = Filesystem :: get_directory_content($dir, Filesystem :: LIST_DIRECTORIES, false);

        foreach ($folders as $folder)
        {
            $content_object = ContentObjectInstaller::factory($folder);
            if ($content_object)
            {
            	$content_object->install();
            	$this->process_result($folder, $result, $content_object->retrieve_message());
            }
        }
        

        // Post-processing for selected applications
    	if (count($applications) > 0)
        {
        	echo '<h3>' . Translation :: get('PostProcessingWebApplications') . '</h3>';
        }
        $count = 0;
        foreach ($applications as $application)
        {
            $toolPath = Path :: get_application_path() . 'lib/' . $application . '/install';
            if (is_dir($toolPath) && WebApplication :: is_application_name($application))
            {
                $check_name = 'install_' . $application;
                if (isset($values[$check_name]) && $values[$check_name] == '1')
                {
                    $installer = Installer :: factory($application, $values);
                    $result = $installer->post_process();
                    $this->process_result($application, $result, $installer->retrieve_message());

                    unset($installer, $result);
                    flush();
                    $count ++;
                }
            }
            flush();
        }
        if ($count == 0)
        {
        	$this->process_result('web_application', true, Translation :: get('NoWebApplicationSelected'));
        }
    }

    function display_install_block_header($application, $result, $default_collapse)
    {
        $counter = $this->counter;

        $html = array();
        $html[] = '<div class="content_object" style="padding: 15px 15px 15px 76px; background-image: url(../layout/aqua/images/admin/place_' . $application . '.png);' . ($counter % 2 == 0 ? 'background-color: #fafafa;' : '') . '">';
        $html[] = '<div class="title">' . Translation :: get(Application :: application_to_class($application)) . '</div>';

        $collapse = '';

        if($result && $default_collapse)
        {
        	$collapse = ' collapse';
        }

        $html[] = '<div class="description' . $collapse . '">';

        return implode("\n", $html);
    }

    function display_install_block_footer()
    {
        $html = array();
        $html[] = '</div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }

    function process_result($application, $result, $message, $default_collapse = true)
    {
        echo $this->display_install_block_header($application, $result, $default_collapse);
        echo $message;
        echo $this->display_install_block_footer();
        if (! $result)
        {
            $this->parent->display_footer();
            exit();
        }

    }
}
?>
<?php

/**
 * $Id: log_viewer.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

require_once 'HTML/Table.php';

/**
 * Admin component
 */
class AdminManagerLogViewerComponent extends AdminManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(AdminManager :: PARAM_ACTION => null)), Translation :: get('PlatformAdministration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('LogsViewer')));
        $trail->add_help('administration');
        
        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT))
        {
            $this->display_header();
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $form = $this->build_form();
        
        $this->display_header();
        echo $form->toHtml() . '<br />';
        
        if ($form->validate())
        {
            $type = $form->exportValue('type');
            $chamilo_type = $form->exportValue('chamilo_type');
            $server_type = $form->exportValue('server_type');
            $lines = $form->exportValue('lines');
        }
        else
        {
            $type = 'chamilo';
            
            $dir = Path :: get(SYS_FILE_PATH) . 'logs/';
            $content = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES, false);
            
            $chamilo_type = $content[0];
            $lines = '10';
        }
        
        $this->display_logfile_table($type, $chamilo_type, $server_type, $lines);
        
        $this->display_footer();
    }

    function build_form()
    {
        $form = new FormValidator('logviewer', 'post', $this->get_url());
        $renderer = & $form->defaultRenderer();
        $renderer->setElementTemplate(' {element} ');
        
        $types = array('server' => Translation :: get('ServerLogs'));
        
        $file = Path :: get(SYS_FILE_PATH) . 'logs/';
       	$scan_list = scandir($file);
       	
       	foreach($scan_list as $i => $item)
       	{
       		if(substr($item, 0, 1) == '.')
       		{
       			unset($scan_list[$i]);
       		}
       	}
       	
       	if(count($scan_list) > 0)
        {
        	$types['chamilo'] = Translation :: get('ChamiloLogs');		
        }
        
        $lines = array('10' => '10 ' . Translation :: get('lines'), '20' => '20 ' . Translation :: get('lines'), '50' => '50 ' . Translation :: get('lines'), 'all' => Translation :: get('AllLines'));
        
        $dir = Path :: get(SYS_FILE_PATH) . 'logs/';
        $content = Filesystem :: get_directory_content($dir, Filesystem :: LIST_FILES, false);
        foreach ($content as $file)
        {
            if (substr($file, 0, 1) == '.')
                continue;
            
            $files[$file] = $file;
        }
        
        $server_types = array('php' => Translation :: get('PHPErrorLog'), 'httpd' => Translation :: get('HTTPDErrorLog'), 'mysql' => Translation :: get('MYSQLErrorLog'));
        
        $form->addElement('select', 'type', '', $types, array('id' => 'type'));
        $form->addElement('select', 'chamilo_type', '', $files, array('id' => 'chamilo_type'));
        
        $form->addElement('select', 'server_type', '', $server_types, array('id' => 'server_type'));
        $form->addElement('select', 'lines', '', $lines);
        
        $form->addElement('submit', 'submit', Translation :: get('Ok'), array('class' => 'positive finish'));
        $form->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/log_viewer.js'));
        
        return $form;
    }

    function display_logfile_table($type, $chamilo_type, $server_type, $count)
    {
        if ($type == 'chamilo')
        {
            $file = Path :: get(SYS_FILE_PATH) . 'logs/' . $chamilo_type;
            $message = Translation :: get('NoLogfilesFound');
        }
        else
        {
            $file = PlatformSetting :: get($server_type . '_error_location');
            $message = Translation :: get('ServerLogfileLocationNotDefined');
        }
        
        if (! file_exists($file) || is_dir($file))
        {
            echo '<div class="warning-message">' . $message . '</div>';
            return;
        }
        
        $table = new HTML_Table(array('style' => 'background-color: lightblue; width: 100%;', 'cellspacing' => 0));
        $this->read_file($file, $table, $count);
        echo $table->toHtml();
    }

    function read_file($file, &$table, $count)
    {
        $fh = fopen($file, 'r');
        $string = file_get_contents($file);
        $lines = explode("\n", $string);
        $lines = array_reverse($lines);
        
        if ($count == 'all' || count($lines) < $count)
            $count = count($lines) - 1;
        
        $row = 0;
        foreach ($lines as $line)
        {
            if ($row >= $count)
                break;
            
            if ($line == '')
                continue;
            
            $border = ($row < $count - 1) ? 'border-bottom: 1px solid black;' : '';
            //$color = ($row % 2 == 0) ? 'background-color: yellow;' : '';
            

            if (stripos($line, 'error') !== false)
                $color = 'background-color: red;';
            elseif (stripos($line, 'warning') !== false)
                $color = 'background-color: pink;';
            else
                $color = null;
            
            $table->setCellContents($row, 0, $line);
            $table->setCellAttributes($row, 0, array('style' => "$border $color padding: 5px;"));
            $row ++;
        }
        
        fclose($fh);
    }
}
?>
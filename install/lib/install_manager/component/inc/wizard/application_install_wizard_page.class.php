<?php
/**
 * $Id: application_install_wizard_page.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component.inc.wizard
 */
require_once dirname(__FILE__) . '/install_wizard_page.class.php';
require_once 'XML/Unserializer.php';

/**
 * Class for application settings page
 * Displays a form where the user can enter the installation settings
 * regarding the applications
 */
class ApplicationInstallWizardPage extends InstallWizardPage
{

    function get_title()
    {
        return Translation :: get('AppSetting');
    }

    function get_info()
    {
        return Translation :: get('AppSettingIntro');
    }

    function buildForm()
    {
        $this->set_lang($this->controller->exportValue('page_language', 'install_language'));
        $this->_formBuilt = true;
        
        $packages = $this->get_package_info();
        
        //		echo '<pre>';
        //		print_r($packages) . '<br />';
        //		echo '</pre>';
        

        $this->get_package_tabs($packages);
        
        $buttons = array();
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('back'), Translation :: get('Previous'), array('class' => 'normal previous'));
        $buttons[] = $this->createElement('style_submit_button', $this->getButtonName('next'), Translation :: get('Next'), array('class' => 'normal next'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->setDefaultAction($this->getButtonName('next'));
    }

    function get_package_info()
    {
        $packages = array();
        $applications = WebApplication :: load_all_from_filesystem(false);
        
        foreach ($applications as $application)
        {
            $xml_data = file_get_contents(Path :: get_application_path() . 'lib/' . $application . '/package.info');
            
            if ($xml_data)
            {
                $unserializer = new XML_Unserializer();
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'array');
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE, true);
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_RETURN_RESULT, true);
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_GUESS_TYPES, true);
                $unserializer->setOption(XML_UNSERIALIZER_OPTION_FORCE_ENUM, array('package', 'dependency'));
                
                // unserialize the document
                $status = $unserializer->unserialize($xml_data);
                
                if (! PEAR :: isError($status))
                {
                    $data = $unserializer->getUnserializedData();
                    if (! isset($packages[$data['package'][0]['category']]))
                    {
                        $packages[$data['package'][0]['category']] = array();
                    }
                    $packages[$data['package'][0]['category']][] = $data['package'][0];
                }
            }
        }
        
        ksort($packages);
        
        return $packages;
    }

    function get_package_tabs($categories)
    {
        $html = array();
        
        $html[] = '<div class="clear"></div>';
        
        $html[] = '<div id="selectbuttons" style="padding-left: 10px; display: none;"><br />';
        $html[] = '<a href="#" id="selectall">' . Translation :: get('SelectAll') . '</a>';
        $html[] = ' - ';
        $html[] = '<a href="#" id="unselectall">' . Translation :: get('UnSelectAll') . '</a>'; 
        $html[] = '</div><br />';
        
        $html[] = '<div id="tabs">';
        $html[] = '<ul>';
        
        // Render the tabs
        $index = 0;
        foreach ($categories as $category => $packages)
        {
            $index ++;
            
            $category_name = Translation :: get(Utilities :: underscores_to_camelcase($category));
            
            $html[] = '<li><a href="#tabs-' . $index . '">';
            $html[] = '<span class="category">';
            $html[] = '<img src="../layout/aqua/images/install/category_' . $category . '.png" border="0" style="vertical-align: middle;" alt="' . $category_name . '" title="' . $category_name . '"/>';
            $html[] = '<span class="title">' . $category_name . '</span>';
            $html[] = '</span>';
            $html[] = '</a></li>';
        }
        
        $html[] = '</ul>';
        
        $this->addElement('html', implode("\n", $html));
        $renderer = $this->defaultRenderer();
        
        $index = 0;
        foreach ($categories as $category => $packages)
        {
            $category_name = Translation :: get(Utilities :: underscores_to_camelcase($category));
            $index ++;
            
            $html = array();
            $html[] = '<h2><img src="../layout/aqua/images/install/category_' . $category . '.png" border="0" style="vertical-align: middle;" alt="' . $category_name . '" title="' . $category_name . '"/>&nbsp;' . $category_name . '</h2>';
            $html[] = '<div class="tab" id="tabs-' . $index . '">';
            $html[] = '<a class="prev"></a>';
            $html[] = '<div class="scrollable">';
            $html[] = '<div class="items">';
            $this->addElement('html', implode("\n", $html));
            
            $count = 0;
            
            foreach ($packages as $package)
            {
                $count ++;
                
                $html = array();
                $html[] = '<div class="vertical_action"' . ($count == 1 ? ' style="border-top: 0px solid #FAFCFC;"' : '') . '>';
                $html[] = '<div class="icon">';
                $html[] = '<a href="#"><img src="../layout/aqua/images/install/application_' . $package['code'] . '.png" alt="' . $package['name'] . '" title="' . $package['name'] . '"/></a>';
                $html[] = '</div>';
                $html[] = '<div class="description">';
                $html[] = '<h4>' . $package['name'] . '</h4>';
                $html[] = $package['description'];
                $html[] = '<br />';
                $this->addElement('html', implode("\n", $html));
                
                $checkbox_name = 'install_' . $package['code'];
                $this->addElement('checkbox', $checkbox_name, '', '', array('class' => 'application_check'));
                $renderer->setElementTemplate('{element}', $checkbox_name);
                
                $this->addElement('html', '</div></div>');
            }
            
            //			$this->accept($renderer);
            

            $html = array();
            $html[] = '</div>';
            $html[] = '</div>';
            $html[] = '<a class="next"></a>';
            $html[] = '<div class="clear"></div>';
            $html[] = '</div>';
            $this->addElement('html', implode("\n", $html));
        }
        
        $html = array();
        $html[] = '</div>';
        $html[] = '<script type="text/javascript" src="../common/javascript/install.js"></script>';
        $this->addElement('html', implode("\n", $html));
        
        return implode("\n", $html);
    }
}
?>
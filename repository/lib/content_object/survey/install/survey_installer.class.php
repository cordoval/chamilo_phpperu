<?php
/**
 * $Id: survey_installer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.install
 */
require_once dirname (__FILE__) . '/../survey_context_template.class.php';
require_once dirname (__FILE__) . '/../context_data_manager/context_data_manager.class.php';
class SurveyContentObjectInstaller extends ContentObjectInstaller
{
    function get_path()
    {
        return dirname(__FILE__);
    }
    
    function install_extra()
    {
    	$survey_context_template = new SurveyContextTemplate();
    	$survey_context_template->set_name('ROOTTEMPLATA');
    	$survey_context_template->set_description('DONOTDELETE');
    	$survey_context_template->set_context_type('NONCONTEXT');
    	$survey_context_template->set_context_type_name('NONAME');
    	$survey_context_template->set_key('NOKEY');
    	$survey_context_template->set_parent_id(0);
    	if ($survey_context_template->create())
    	{
           	$this->add_message(Installer::TYPE_NORMAL, Translation :: get('DefaultSurveyContextTemplateCreated'));
           	
    	}
    	else
    	{
    		$this->add_message(Installer::TYPE_ERROR, Translation :: get('DefaultSurveyContextTemplateFailed'));
    		return false;
    	}

    }
}
?>
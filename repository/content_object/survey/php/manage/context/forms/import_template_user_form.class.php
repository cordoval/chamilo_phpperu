<?php
namespace repository\content_object\survey;

use common\libraries\OrCondition;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use user\User;
use user\UserDataManager;
use admin\AdminDataManager;
use rights\RightsDataManager;
use common\libraries\Path;
use PHPExcel_Reader_Excel2007;

require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

class ImportTemplateUserForm extends FormValidator
{
    
    const IMPORT_FILE_NAME = 'template_file';
    
    private $template_user;
    private $context_template;
    private $template_manager;
    private $valid_email_regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

    function __construct($template_manager, $action, $context_template_id)
    {
        parent :: __construct('survey_import_template_user_form', 'post', $action);
        $this->template_manager = $template_manager;
        $this->context_template = SurveyContextDataManager :: get_instance()->retrieve_survey_context_template($context_template_id);
        $this->template_user = SurveyTemplateUser:: factory($this->context_template->get_type());
        $this->template_user->set_context_template_id($context_template_id);
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('category', Translation :: get('template_user'));
        $template_user_properties = array();
        $template_user_properties[] = Translation :: get('Email');
        $template_user_properties = array_merge($template_user_properties, $this->template_user->get_additional_property_names());
        $properties = implode(', ', $template_user_properties);
        $this->add_information_message(null, null, Translation :: get('ExcelfileWithFolowingPropertiesWithRespectOfOrder') . ' : ' . $properties);
        $this->addElement('file', self :: IMPORT_FILE_NAME, Translation :: get('FileName'));
        $this->addElement('category');
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Import'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        parent :: setDefaults($defaults);
    }

    function process()
    {
        
        $values = $this->exportValues();
        $array = explode('.', $_FILES[self :: IMPORT_FILE_NAME]['name']);
        $type = $array[count($array) - 1];
        
        if ($type != 'xlsx')
        {
            $success = false;
        }
        
        $PhpReader = new PHPExcel_Reader_Excel2007();
        $excel = $PhpReader->load($_FILES[self :: IMPORT_FILE_NAME]['tmp_name']);
        $worksheet = $excel->getActiveSheet();
        
        $excel_array = $worksheet->toArray();
        $template_users = array();
        
        $template_user_properties = $this->template_user->get_additional_property_names(true);
        
        //each row in excel file except row 1 = headers !
        for($i = 2; $i < count($excel_array) + 1; $i ++)
        {
            
            $email = $excel_array[$i][0];
            $users = UserDataManager :: get_instance()->retrieve_users_by_email($email);
            foreach ($users as $user)
            {
                $this->template_user->set_user_id($user->get_id());
                $index = 0;
                foreach ($template_user_properties as $template_user_property => $context_type)
                {
                    $index++;
                   	$dummy_context = SurveyContext :: factory($context_type);
                    
                    $key_propertie_names = $dummy_context->get_allowed_keys();
                    
                    if (count($key_propertie_names) > 0)
                    {
                        $conditions = array();
                        $value = $excel_array[$i][$index];
                        foreach ($key_propertie_names as $key_propertie_name)
                        {
                            $conditions[] = new EqualityCondition($key_propertie_name, $value);
                        }
                        $condition = new OrCondition($conditions);
                        $contexts = SurveyContextDataManager :: get_instance()->retrieve_survey_contexts($context_type, $condition);
                        $context = $contexts->next_result();
                       
                    }
                    else
                    {
                        $context_id = $excel_array[$i][$index];
                        $context = SurveyContextDataManager :: get_instance()->retrieve_survey_context_by_id($context_id, $context_type);
                    }
                           
                    if ($context)
                    {
                        $this->template_user->set_additional_property($template_user_property, $context->get_id());
                    }
                    else
                    {
                        continue;
                    }
                }
                $succes = $this->template_user->create();
            }
        }
        return $success;
    }
}
?>
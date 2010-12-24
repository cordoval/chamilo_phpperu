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
use PHPExcel_Reader_Excel5;
use PHPExcel_Reader_OOCalc;

require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

class ImportContextUserForm extends FormValidator
{
    
    const IMPORT_FILE_NAME = 'context_user_file';
    
    private $context_registration_id;
    private $context;
    private $context_type;
    private $valid_email_regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

    function __construct($context_manager, $action, $context_registration_id)
    {
        parent :: __construct('survey_import_context_user_form', 'post', $action);
        $context_registration = SurveyContextDataManager :: get_instance()->retrieve_survey_context_registration($context_registration_id);
        $this->context_type = $context_registration->get_type();
        $this->context = SurveyContext :: factory($this->context_type);
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('category', Translation :: get('context_user'));
        $context_user_properties = array();
        $context_user_properties[] = Translation :: get('Email');
        $context_user_properties = array_merge($context_user_properties, $this->context->get_allowed_keys());
        $properties = implode(', ', $context_user_properties);
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
        
        switch ($type)
        {
            case 'xlsx' :
                $PhpReader = new PHPExcel_Reader_Excel2007();
                $excel = $PhpReader->load($_FILES[self :: IMPORT_FILE_NAME]['tmp_name']);
                break;
            case 'ods' :
                $PhpReader = new PHPExcel_Reader_OOCalc();
                $excel = $PhpReader->load($_FILES[self :: IMPORT_FILE_NAME]['tmp_name']);
                break;
            case 'xls' :
                $PhpReader = new PHPExcel_Reader_Excel5();
                $excel = $PhpReader->load($_FILES[self :: IMPORT_FILE_NAME]['tmp_name']);
                break;
            default :
                return false;
                break;
        }
        
        $worksheet = $excel->getSheet(0);
        $excel_array = $worksheet->toArray();
               
        $context_users = array();
         
        $context_user = new SurveyContextRelUser();
              
        $key_property_name = $excel_array[1][1];
           
        
        $key_propertie_names = $this->context->get_allowed_keys();
              
        if (! in_array($key_property_name, $key_propertie_names))
        {
			return false;
        }
        
        //each row in excel file except row 1 = headers !
        for($i = 2; $i < count($excel_array) + 1; $i ++)
        {
            
            $email = $excel_array[$i][0];
            $users = UserDataManager :: get_instance()->retrieve_users_by_email($email);
            foreach ($users as $user)
            {
                $context_user->set_user_id($user->get_id());
                $value = $excel_array[$i][1];
                $condition = new EqualityCondition($key_property_name, $value, $this->context->get_table_name());
                $context = SurveyContextDataManager :: get_instance()->retrieve_survey_contexts($this->context_type, $condition)->next_result();
                if (isset($context))
                {
                	$context_user->set_context_id($context->get_id());
                    $succes = $context_user->create();
                }
            
            }
        }
        return $success;
    }
}
?>
<?php
namespace repository\content_object\survey;

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

class ImportContextForm extends FormValidator
{
    
    const IMPORT_FILE_NAME = 'context_file';
    
    private $context;
    private $context_manager;
    private $valid_email_regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

    function __construct($context_manager, $action, $context_registration_id)
    {
        parent :: __construct('survey_import_context_form', 'post', $action);
        $this->context_manager = $context_manager;
        $context_registration = SurveyContextDataManager :: get_instance()->retrieve_survey_context_registration($context_registration_id);
        $this->context = SurveyContext :: factory($context_registration->get_type());
        $this->context->set_type($context_registration->get_type());
        $this->context->set_active(1);
        $this->context->set_context_registration_id($context_registration_id);
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('category', Translation :: get('Context'));
        $context_properties = array();
        $context_properties[] = Translation :: get('Name');
        $context_properties = array_merge($context_properties, $this->context->get_additional_property_names());
        $properties = implode(', ', $context_properties);
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
        $contexts = array();
        
        $context_properties = $this->context->get_additional_property_names();
        
//        dump($context_properties);
        
        //each row in excel file except row 1 = headers !
        for($i = 2; $i < count($excel_array) + 1; $i ++)
        {
            $context_name = $excel_array[$i][0];
            $this->context->set_name($context_name);
            $index = 1;
            foreach ($context_properties as $context_property)
            {
                $this->context->set_additional_property($context_property, $excel_array[$i][$index]);
                $index ++;
            }
          
            $success = $this->context->create();
        }
        return $success;
    }
}
?>
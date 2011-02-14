<?php
namespace application\survey;

use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Translation;
use common\libraries\FormValidator;
use user\UserDataManager;
use rights\RightsUtilities;
use rights\RightsDataManager;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_Excel5;
use PHPExcel_Reader_OOCalc;

ini_set("memory_limit", "-1");
ini_set("max_execution_time", "0");

require_once Path :: get_plugin_path() . 'phpexcel/PHPExcel.php';

class SurveySubscribeUserForm extends FormValidator
{
    
    const APPLICATION_NAME = 'survey';
    const PARAM_TARGET = 'target_users_and_groups';
    const PARAM_TARGET_OPTION = 'target_users_and_groups_option';
    const PARAM_RIGHTS = 'rights';
    
    const TYPE_SELECT_USERS = 'select_users';
    const TYPE_IMPORT_EMAILS = 'import_emails';
    
    const IMPORT_FILE_NAME = 'context_user_file';
    private $valid_email_regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';
    
    private $parent;
    private $publication;
    private $user;
    private $type;

    function __construct($type, $publication, $action, $user)
    {
        parent :: __construct('subscribe_users', 'post', $action);
        
        $this->publication = $publication;
        $this->user = $user;
        if ($type == self :: TYPE_SELECT_USERS)
        {
            $this->build_select_user_form();
        }
        else
        {
            $this->build_import_email_form();
        }
        
        $this->setDefaults();
    }

    function build_import_email_form()
    {
        $publication = $this->publication;
        
        $this->addElement('category', Translation :: get('UserEmails'));
        $this->add_information_message(null, null, Translation :: get('ExcelfileWithFirstColumnOfEmails'));
        $this->addElement('file', self :: IMPORT_FILE_NAME, Translation :: get('FileName'));
        
        $rights = SurveyRights :: get_available_rights_for_publications();
        foreach ($rights as $right_name => $right)
        {
            $check_boxes[] = $this->createElement('checkbox', $right, $right_name, $right_name . '  ');
        }
        $this->addGroup($check_boxes, self :: PARAM_RIGHTS, Translation :: get('Rights'), '&nbsp;', true);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SubscribeEmails'), array(
                'class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array(
                'class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('category');
        $this->addElement('html', '<br />');
    }

    function build_select_user_form()
    {
        $publication = $this->publication;
        
        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'user/php/xml_feeds/xml_user_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        $attributes['defaults'] = array();
        $attributes['options'] = array('load_elements' => false);
        
        $this->add_receivers(self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes);
        
        $rights = SurveyRights :: get_available_rights_for_publications();
        foreach ($rights as $right_name => $right)
        {
            $check_boxes[] = $this->createElement('checkbox', $right, $right_name, $right_name . '  ');
        }
        $this->addGroup($check_boxes, self :: PARAM_RIGHTS, Translation :: get('Rights'), '&nbsp;', true);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('SubscribeUsers'), array(
                'class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array(
                'class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('category');
        $this->addElement('html', '<br />');
        $defaults[self :: APPLICATION_NAME . '_opt_forever'] = 1;
        $defaults[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] = 0;
        $this->setDefaults($defaults);
    
    }

    function create_user_rights()
    {
        $publication_id = $this->publication->get_id();
        
        $values = $this->exportValues();
        
        $succes = false;
        
        $location_id = SurveyRights :: get_location_id_by_identifier_from_surveys_subtree($publication_id, SurveyRights :: TYPE_PUBLICATION);
        
        if ($values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET_OPTION] == 0)
        {
            //all users of the system will be subscribed if not allready subscribed
            $users = UserDataManager :: get_instance()->retrieve_users();
            
            while ($user = $users->next_result())
            {
                $user_id = $user->get_id();
                
                foreach ($values[self :: PARAM_RIGHTS] as $right => $value)
                {
                    if ($value == 1)
                    {
                        $succes = RightsUtilities :: set_user_right_location_value($right, $user_id, $location_id, 1);
                    }
                }
            
            }
        }
        else
        {
            $user_ids = $values[self :: APPLICATION_NAME . '_opt_' . self :: PARAM_TARGET . '_elements']['user'];
            
            if (count($user_ids))
            {
                foreach ($user_ids as $user_id)
                {
                    foreach ($values[self :: PARAM_RIGHTS] as $right => $value)
                    {
                        if ($value == 1)
                        {
                            $succes = RightsUtilities :: set_user_right_location_value($right, $user_id, $location_id, 1);
                        }
                    }
                }
            }
        }
        
        return $succes;
    }

    function create_email_rights()
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
        
        //        dump($excel_array);
        
        $no_user_emails = array();
        $publication_id = $this->publication->get_id();
        $location_id = SurveyRights :: get_location_id_by_identifier_from_surveys_subtree($publication_id, SurveyRights :: TYPE_PUBLICATION);
        
        
        //each row in excel file starting at row 1,  no header!
        for($i = 1; $i < count($excel_array) + 1; $i ++)
        {
            
            $email = $excel_array[$i][0];
            $users = UserDataManager :: get_instance()->retrieve_users_by_email($email);
            if(count($users)>0){
             foreach ($users as $user)
            {
                $user_id = $user->get_id();
                foreach ($values[self :: PARAM_RIGHTS] as $right => $value)
                {
                    if ($value == 1)
                    {
                        $succes = RightsUtilities :: set_user_right_location_value($right, $user_id, $location_id, 1);
                    }
                }
            }
            }else{
            	$no_user_emails[] = $email;
            }
           
        }
        //        exit;
        return $no_user_emails;
    }

}

?>
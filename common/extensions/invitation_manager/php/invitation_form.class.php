<?php
namespace common\extensions\invitation_manager;

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

class InvitationForm extends FormValidator
{
    
    const IMPORT_FILE_NAME = 'email_address_file';
    
    private $invitation_manager;
    private $valid_email_regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

    function __construct($invitation_manager, $action)
    {
        parent :: __construct('invitation_form', 'post', $action);
        $this->invitation_manager = $invitation_manager;
        $this->build_form();
        $this->setDefaults();
    }

    function build_form()
    {
        $this->addElement('category', Translation :: get('Invitation'));
        
        $this->add_information_message(null, null, Translation :: get('CommaSeparatedListOfEmailAddresses'));
        $this->addElement('file', self :: IMPORT_FILE_NAME, Translation :: get('FileName'));
        $this->addElement('textarea', Invitation :: PROPERTY_EMAIL, Translation :: get('EmailAddresses'), 'cols="70" rows="8"');
        //        $this->addRule(Invitation :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->add_forever_or_expiration_date_window(Invitation :: PROPERTY_EXPIRATION_DATE);
        $this->addElement('checkbox', Invitation :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'), null, 1);
        
        //        $rights_templates = RightsDataManager :: get_instance()->retrieve_rights_templates();
        //        while ($rights_template = $rights_templates->next_result())
        //        {
        //            $defaults[$rights_template->get_id()] = array('title' => $rights_template->get_name(), 'description', $rights_template->get_description(), 'class' => 'rights_template');
        //        }
        //
        //        $url = Path :: get(WEB_PATH) . 'rights/xml_feeds/xml_rights_template_feed.php';
        //        $locale = array();
        //        $locale['Display'] = Translation :: get('AddRightsTemplates');
        //        $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
        //        $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
        //        $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);
        //        $hidden = true;
        //
        //        $element_finder = $this->addElement('element_finder', 'rights_templates', null, $url, $locale, array());
        //        $element_finder->setDefaultCollapsed(true);
        

        $this->addElement('category');
        
        $this->addElement('category', Translation :: get('InvitationMessage'));
        $this->add_textfield(Invitation :: PROPERTY_TITLE, Translation :: get('InvitationSubject'), true);
        //$this->addElement('text', Invitation :: PROPERTY_TITLE, Translation :: get('InvitationSubject'));
        $this->addRule(Invitation :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->add_html_editor(Invitation :: PROPERTY_MESSAGE, Translation :: get('InvitationBody'), true);
        $this->addElement('category');
        
        $checkboxes = array();
        $checkboxes[] = '<script type="text/javascript">';
        $checkboxes[] = '$(document).ready(function() {';
        $checkboxes[] = '$("input:checkbox[name=\'' . Invitation :: PROPERTY_ANONYMOUS . '\']").iphoneStyle({ checkedLabel: \'' . Translation :: get('ConfirmYes', null, Utilities :: COMMON_LIBRARIES) . '\', uncheckedLabel: \'' . Translation :: get('ConfirmNo', null, Utilities :: COMMON_LIBRARIES) . '\'});';
        $checkboxes[] = '});';
        $checkboxes[] = '</script>';
        $this->addElement('html', implode("\n", $checkboxes));
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Invite'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        
        $expiration_date = $this->invitation_manager->get_parent()->get_expiration_date();
        $defaults[Invitation :: PROPERTY_ANONYMOUS] = 0;
        
        if ($expiration_date == 0)
        {
            $defaults['forever'] = 1;
        }
        else
        {
            $defaults['forever'] = 0;
            $defaults[Invitation :: PROPERTY_EXPIRATION_DATE] = $expiration_date;
        }
        parent :: setDefaults($defaults);
    }

    //    function render()
    //    {
    //        $this->form->addElement('category', Translation :: get('InviteExternalUsers'));
    //        $this->form->add_information_message(null, null, Translation :: get('CommaSeparatedListOfEmailAddresses'));
    //        $this->form->addElement('textarea', Invitation :: PROPERTY_EMAIL, Translation :: get('EmailAddresses'), 'cols="70" rows="8"');
    //        $this->form->addRule(Invitation :: PROPERTY_EMAIL, Translation :: get('ThisFieldIsRequired'), 'required');
    //        $this->form->add_forever_or_expiration_date_window(Invitation :: PROPERTY_EXPIRATION_DATE);
    //        $this->form->addElement('checkbox', Invitation :: PROPERTY_ANONYMOUS, Translation :: get('Anonymous'), null, 1);
    //
    //        $rights_templates = RightsDataManager :: get_instance()->retrieve_rights_templates();
    //        while ($rights_template = $rights_templates->next_result())
    //        {
    //            $defaults[$rights_template->get_id()] = array('title' => $rights_template->get_name(), 'description', $rights_template->get_description(), 'class' => 'rights_template');
    //        }
    //
    //        $url = Path :: get(WEB_PATH) . 'rights/xml_feeds/xml_rights_template_feed.php';
    //        $locale = array();
    //        $locale['Display'] = Translation :: get('AddRightsTemplates');
    //        $locale['Searching'] = Translation :: get('Searching');
    //        $locale['NoResults'] = Translation :: get('NoResults');
    //        $locale['Error'] = Translation :: get('Error');
    //        $hidden = true;
    //
    //        //        $element_finder = $this->form->addElement('element_finder', 'rights_templates', null, $url, $locale, array());
    //        //        $element_finder->setDefaultCollapsed(true);
    //
    //
    //        if ($this->show_message_fields)
    //        {
    //            $this->form->addElement('text', Invitation :: PROPERTY_TITLE, Translation :: get('Subject'));
    //            $this->form->addRule(Invitation :: PROPERTY_TITLE, Translation :: get('ThisFieldIsRequired'), 'required');
    //            $this->form->add_html_editor(Invitation :: PROPERTY_MESSAGE, Translation :: get('Message'), true);
    //        }
    //
    //        $this->form->addElement('category');
    //
    //        $checkboxes = array();
    //        $checkboxes[] = '<script type="text/javascript">';
    //        $checkboxes[] = '$(document).ready(function() {';
    //        $checkboxes[] = '$("input:checkbox[name=\'' . Invitation :: PROPERTY_ANONYMOUS . '\']").iphoneStyle({ checkedLabel: \'' . Translation :: get('Yes') . '\', uncheckedLabel: \'' . Translation :: get('No') . '\'});';
    //        $checkboxes[] = '});';
    //        $checkboxes[] = '</script>';
    //
    //        $this->form->addElement('html', implode("\n", $checkboxes));
    //        $this->form->setDefaults(array('forever' => 1));
    //    }
    

    function process()
    {
        
        $values = $this->exportValues();
//        dump($values);
        
//        dump($_FILES);
        
        $array = explode('.', $_FILES[self :: IMPORT_FILE_NAME]['name']);
//        dump($array);
        $type = $array[count($array) - 1];
        
        if ($type != 'xlsx')
        {
//            dump('false');
        
     //        	return false;
        }
        
//        dump($type);
        $PhpReader = new PHPExcel_Reader_Excel2007();
        $excel = $PhpReader->load($_FILES[self :: IMPORT_FILE_NAME]['tmp_name']);
        $worksheet = $excel->getActiveSheet();
        
        $excel_array = $worksheet->toArray();
        $emails = array();
        
        //elke regel in het excel bestand aflopen behalve rij 1 = headers !
        for($i = 2; $i < count($excel_array) + 1; $i ++)
        {
            $emails[] = $excel_array[$i][0];
        }
        
        $invitation_parameters = new InvitationParameters();
        $invitation_parameters->set_emails_from_array($emails);
        
        $emails = $values[Invitation :: PROPERTY_EMAIL];
        if ($emails)
        {
            $invitation_parameters->set_emails_from_string($emails);
        }
        
        $emails = $invitation_parameters->get_emails();
//        dump($invitation_parameters->get_emails());
       
        $emails = $invitation_parameters->get_emails();
        $properties = $invitation_parameters->get_properties();
        
        $existing_users = array();
        
        $parameters = $this->invitation_manager->get_parent()->get_url_parameters();
        $location_rights_ids = $this->invitation_manager->get_parent()->get_location_rights_ids();
        $anonymous = $values[Invitation :: PROPERTY_ANONYMOUS];
        if(!$anonymous){
        	$anonymous = 0;
        }
        
        foreach ($emails as $email)
        {
            $email_condition = new EqualityCondition(User :: PROPERTY_EMAIL, $email);
            $users = UserDataManager :: get_instance()->retrieve_users($email_condition);
            
            if ($users->size() > 0)
            {
                while ($user = $users->next_result())
                {
                    $existing_users[] = $user->get_id();
                }
            }
            else
            {
                $invitation_conditions = array();
                $invitation_conditions[] = new EqualityCondition(Invitation :: PROPERTY_EMAIL, $email);
                $invitation_conditions[] = new EqualityCondition(Invitation :: PROPERTY_PARAMETERS, $parameters);
                $invitation_condition = new AndCondition($invitation_conditions);
                
                $count = AdminDataManager :: get_instance()->count_invitations($invitation_condition);
                
                if ($count > 0)
                {
                    $invitations = AdminDataManager :: get_instance()->retrieve_invitations($invitation_condition);
                    
                    while ($invitation = $invitations->next_result())
                    {
                        $invitation->set_expiration_date($values[Invitation :: PROPERTY_EXPIRATION_DATE]);
                        $invitation->set_anonymous($anonymous);
                        $invitation->set_title($values[Invitation :: PROPERTY_TITLE]);
                        $invitation->set_message($values[Invitation :: PROPERTY_MESSAGE]);
                        //                        $invitation->set_rights_templates($properties[Invitation :: PROPERTY_RIGHTS_TEMPLATES]['template']);
                        $invitation->update();
                    }
                }
                else
                {
                    $invitation = new Invitation();
                    $invitation->set_email($email);
                    $invitation->set_parameters($parameters);
                    $invitation->set_rights_templates($location_rights_ids);
                    $invitation->set_expiration_date($values[Invitation :: PROPERTY_EXPIRATION_DATE]);
                    $invitation->set_anonymous($anonymous);
                    $invitation->set_title($values[Invitation :: PROPERTY_TITLE]);
                    $invitation->set_message($values[Invitation :: PROPERTY_MESSAGE]);
                    $succes = $invitation->create();
                }
            }
//            dump($succes);
//            dump($invitation);
        }
        
//       exit;
        
        $success = $this->invitation_manager->get_parent()->process_existing_users($existing_users);
        
        return $success;
    }
}
?>
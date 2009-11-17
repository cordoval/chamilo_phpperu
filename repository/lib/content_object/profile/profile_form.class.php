<?php
/**
 * $Id: profile_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.profile
 */
require_once dirname(__FILE__) . '/profile.class.php';

class ProfileForm extends ContentObjectForm
{

    protected function build_creation_form()
    {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->build_default_form();
        $this->addElement('category');
    }

    protected function build_editing_form()
    {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->build_default_form();
        $this->addElement('category');
    }

    private function build_default_form()
    {
        $this->add_html_editor(Profile :: PROPERTY_ADDRESS, Translation :: get('Address'), false);
        $this->add_textfield(Profile :: PROPERTY_PHONE, Translation :: get('Phone'), false, 'size="40"');
        $this->add_textfield(Profile :: PROPERTY_FAX, Translation :: get('Fax'), false, 'size="40"');
        $this->add_textfield(Profile :: PROPERTY_MAIL, Translation :: get('Mail'), false, 'size="40"');
        $this->addRule(Profile :: PROPERTY_MAIL, Translation :: get('InvalidEmail'), 'email');
        $this->add_html_editor(Profile :: PROPERTY_COMPETENCES, Translation :: get('Competences'), false);
        $this->add_html_editor(Profile :: PROPERTY_DIPLOMAS, Translation :: get('Diplomas'), false);
        $this->add_html_editor(Profile :: PROPERTY_TEACHING, Translation :: get('Teaching'), false);
        $this->add_html_editor(Profile :: PROPERTY_OPEN, Translation :: get('Open'), false);
        $this->add_textfield(Profile :: PROPERTY_SKYPE, Translation :: get('Skype'), false, 'size="40"');
        $this->add_textfield(Profile :: PROPERTY_MSN, Translation :: get('Msn'), false, 'size="40"');
        $this->add_textfield(Profile :: PROPERTY_AIM, Translation :: get('Aim'), false, 'size="40"');
        $this->add_textfield(Profile :: PROPERTY_YIM, Translation :: get('Yim'), false, 'size="40"');
        $this->add_textfield(Profile :: PROPERTY_ICQ, Translation :: get('Icq'), false, 'size="40"');
        $this->addElement('checkbox', Profile :: PROPERTY_PICTURE, Translation :: get('IncludeAccountPicture'));
    
    }

    function setDefaults($defaults = array ())
    {
        $lo = $this->get_content_object();
        if (isset($lo))
        {
            $defaults[Profile :: PROPERTY_COMPETENCES] = $lo->get_competences();
            $defaults[Profile :: PROPERTY_DIPLOMAS] = $lo->get_diplomas();
            $defaults[Profile :: PROPERTY_TEACHING] = $lo->get_teaching();
            $defaults[Profile :: PROPERTY_OPEN] = $lo->get_open();
            $defaults[Profile :: PROPERTY_PHONE] = $lo->get_phone();
            $defaults[Profile :: PROPERTY_FAX] = $lo->get_fax();
            $defaults[Profile :: PROPERTY_ADDRESS] = $lo->get_address();
            $defaults[Profile :: PROPERTY_MAIL] = $lo->get_mail();
            $defaults[Profile :: PROPERTY_SKYPE] = $lo->get_skype();
            $defaults[Profile :: PROPERTY_MSN] = $lo->get_msn();
            $defaults[Profile :: PROPERTY_YIM] = $lo->get_yim();
            $defaults[Profile :: PROPERTY_AIM] = $lo->get_aim();
            $defaults[Profile :: PROPERTY_ICQ] = $lo->get_icq();
            $defaults[Profile :: PROPERTY_PICTURE] = $lo->get_picture();
        }
        
        parent :: setDefaults($defaults);
    }

    function set_csv_values($valuearray)
    {
        $defaults[ContentObject :: PROPERTY_TITLE] = $valuearray[0];
        $defaults[ContentObject :: PROPERTY_PARENT_ID] = $valuearray[1];
        $defaults[ContentObject :: PROPERTY_DESCRIPTION] = $valuearray[2];
        $defaults[Profile :: PROPERTY_COMPETENCES] = $valuearray[3];
        $defaults[Profile :: PROPERTY_DIPLOMAS] = $valuearray[4];
        $defaults[Profile :: PROPERTY_MAIL] = $valuearray[5];
        $defaults[Profile :: PROPERTY_TEACHING] = $valuearray[6];
        $defaults[Profile :: PROPERTY_OPEN] = $valuearray[7];
        $defaults[Profile :: PROPERTY_PHONE] = $valuearray[8];
        $defaults[Profile :: PROPERTY_FAX] = $valuearray[9];
        $defaults[Profile :: PROPERTY_ADDRESS] = $valuearray[10];
        $defaults[Profile :: PROPERTY_SKYPE] = $valuearray[11];
        $defaults[Profile :: PROPERTY_MSN] = $valuearray[12];
        $defaults[Profile :: PROPERTY_YIM] = $valuearray[13];
        $defaults[Profile :: PROPERTY_AIM] = $valuearray[14];
        $defaults[Profile :: PROPERTY_ICQ] = $valuearray[15];
        $defaults[Profile :: PROPERTY_PICTURE] = $valuearray[16];
        
        parent :: set_values($defaults);
    }

    function create_content_object()
    {
        $object = new Profile();
        $this->fill_properties($object);
        parent :: set_content_object($object);
        return parent :: create_content_object();
    }

    function update_content_object()
    {
        $object = $this->get_content_object();
        $this->fill_properties($object);
        parent :: set_content_object($object);
        return parent :: update_content_object();
    }

    private function fill_properties($object)
    {
        $object->set_competences($this->exportValue(Profile :: PROPERTY_COMPETENCES));
        $object->set_diplomas($this->exportValue(Profile :: PROPERTY_DIPLOMAS));
        $object->set_teaching($this->exportValue(Profile :: PROPERTY_TEACHING));
        $object->set_open($this->exportValue(Profile :: PROPERTY_OPEN));
        $object->set_phone($this->exportValue(Profile :: PROPERTY_PHONE));
        $object->set_fax($this->exportValue(Profile :: PROPERTY_FAX));
        $object->set_address($this->exportValue(Profile :: PROPERTY_ADDRESS));
        $object->set_mail($this->exportValue(Profile :: PROPERTY_MAIL));
        $object->set_skype($this->exportValue(Profile :: PROPERTY_SKYPE));
        $object->set_msn($this->exportValue(Profile :: PROPERTY_MSN));
        $object->set_yim($this->exportValue(Profile :: PROPERTY_YIM));
        $object->set_aim($this->exportValue(Profile :: PROPERTY_AIM));
        $object->set_icq($this->exportValue(Profile :: PROPERTY_ICQ));
        $object->set_picture($this->exportValue(Profile :: PROPERTY_PICTURE));
    }
}
?>

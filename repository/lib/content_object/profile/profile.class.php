<?php
/**
 * $Id: profile.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.profile
 */
/**
 * This class represents a calendar event
 */
class Profile extends ContentObject
{
	const CLASS_NAME = __CLASS__;

	static function get_type_name() 
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}
	
	/**
     * The start date of the calendar event
     */
    const PROPERTY_COMPETENCES = 'competences';
    const PROPERTY_DIPLOMAS = 'diplomas';
    const PROPERTY_TEACHING = 'teaching';
    const PROPERTY_OPEN = 'open';
    const PROPERTY_PHONE = 'phone';
    const PROPERTY_FAX = 'fax';
    const PROPERTY_ADDRESS = 'address';
    const PROPERTY_MAIL = 'mail';
    const PROPERTY_SKYPE = 'skype';
    const PROPERTY_MSN = 'msn';
    const PROPERTY_YIM = 'yim';
    const PROPERTY_AIM = 'aim';
    const PROPERTY_ICQ = 'icq';
    const PROPERTY_PICTURE = 'picture';

    function get_competences()
    {
        return $this->get_additional_property(self :: PROPERTY_COMPETENCES);
    }

    function set_competences($competences)
    {
        return $this->set_additional_property(self :: PROPERTY_COMPETENCES, $competences);
    }

    function get_diplomas()
    {
        return $this->get_additional_property(self :: PROPERTY_DIPLOMAS);
    }

    function set_diplomas($diplomas)
    {
        return $this->set_additional_property(self :: PROPERTY_DIPLOMAS, $diplomas);
    }

    function get_teaching()
    {
        return $this->get_additional_property(self :: PROPERTY_TEACHING);
    }

    function set_teaching($teaching)
    {
        return $this->set_additional_property(self :: PROPERTY_TEACHING, $teaching);
    }

    function get_open()
    {
        return $this->get_additional_property(self :: PROPERTY_OPEN);
    }

    function set_open($open)
    {
        return $this->set_additional_property(self :: PROPERTY_OPEN, $open);
    }

    function get_phone()
    {
        return $this->get_additional_property(self :: PROPERTY_PHONE);
    }

    function set_phone($phone)
    {
        return $this->set_additional_property(self :: PROPERTY_PHONE, $phone);
    }

    function get_fax()
    {
        return $this->get_additional_property(self :: PROPERTY_FAX);
    }

    function set_fax($fax)
    {
        return $this->set_additional_property(self :: PROPERTY_FAX, $fax);
    }

    function get_address()
    {
        return $this->get_additional_property(self :: PROPERTY_ADDRESS);
    }

    function set_address($address)
    {
        return $this->set_additional_property(self :: PROPERTY_ADDRESS, $address);
    }

    function get_mail()
    {
        return $this->get_additional_property(self :: PROPERTY_MAIL);
    }

    function set_mail($mail)
    {
        return $this->set_additional_property(self :: PROPERTY_MAIL, $mail);
    }

    function get_skype()
    {
        return $this->get_additional_property(self :: PROPERTY_SKYPE);
    }

    function set_skype($skype)
    {
        return $this->set_additional_property(self :: PROPERTY_SKYPE, $skype);
    }

    function get_msn()
    {
        return $this->get_additional_property(self :: PROPERTY_MSN);
    }

    function set_msn($msn)
    {
        return $this->set_additional_property(self :: PROPERTY_MSN, $msn);
    }

    function get_yim()
    {
        return $this->get_additional_property(self :: PROPERTY_YIM);
    }

    function set_yim($yim)
    {
        return $this->set_additional_property(self :: PROPERTY_YIM, $yim);
    }

    function get_aim()
    {
        return $this->get_additional_property(self :: PROPERTY_AIM);
    }

    function set_aim($aim)
    {
        return $this->set_additional_property(self :: PROPERTY_AIM, $aim);
    }

    function get_icq()
    {
        return $this->get_additional_property(self :: PROPERTY_ICQ);
    }

    function set_icq($icq)
    {
        return $this->set_additional_property(self :: PROPERTY_ICQ, $icq);
    }

    function get_picture()
    {
        return $this->get_additional_property(self :: PROPERTY_PICTURE);
    }

    function set_picture($picture)
    {
        if (is_null($picture))
        {
            $picture = 0;
        }
        return $this->set_additional_property(self :: PROPERTY_PICTURE, $picture);
    }

    /**
     * Attachments are supported by calendar events
     * @return boolean Always true
     */
    function supports_attachments()
    {
        return false;
    }

    function is_versionable()
    {
        return false;
    }

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_ADDRESS, self :: PROPERTY_AIM, self :: PROPERTY_COMPETENCES, self :: PROPERTY_DIPLOMAS, self :: PROPERTY_FAX, self :: PROPERTY_ICQ, self :: PROPERTY_MAIL, self :: PROPERTY_MSN, self :: PROPERTY_OPEN, self :: PROPERTY_PHONE, self :: PROPERTY_PICTURE, self :: PROPERTY_SKYPE, self :: PROPERTY_TEACHING, self :: PROPERTY_YIM);
    }
}
?>
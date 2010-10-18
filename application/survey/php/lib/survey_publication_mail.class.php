<?php

class SurveyPublicationMail extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication_mail';
    
    /**
     * SurveyPublicationMail properties
     */
    const PROPERTY_MAIL_HEADER = 'mail_header';
    const PROPERTY_MAIL_CONTENT = 'mail_content';
    const PROPERTY_FROM_ADDRES = 'from_address';
    const PROPERTY_FROM_ADDRES_NAME = 'from_address_name';
    const PROPERTY_REPLY_ADDRES = 'reply_address';
    const PROPERTY_REPLY_ADDRES_NAME = 'reply_address_name';
    const PROPERTY_SENDER_USER_ID = 'sender_user_id';

    /**
     * Get the default properties
     * @return array The property names.
     */
    
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_FROM_ADDRES,self :: PROPERTY_FROM_ADDRES_NAME ,self ::PROPERTY_REPLY_ADDRES, self :: PROPERTY_REPLY_ADDRES_NAME ,self :: PROPERTY_MAIL_CONTENT, self :: PROPERTY_MAIL_HEADER, self :: PROPERTY_SENDER_USER_ID));
    }

    function get_data_manager()
    {
        return SurveyDataManager :: get_instance();
    }

    /**
     * Returns the mail_header of this SurveyPublicationMail.
     * @return the mail_header.
     */
    function get_mail_header()
    {
        return $this->get_default_property(self :: PROPERTY_MAIL_HEADER);
    }

    /**
     * Sets the mail_header of this SurveyPublicationMail.
     * @param mail_header
     */
    function set_mail_haeder($mail_header)
    {
        $this->set_default_property(self :: PROPERTY_MAIL_HEADER, $mail_header);
    }

    /**
     * Sets the mail_content of this SurveyPublicationMail.
     * @param mail_content
     */
    function set_mail_content($mail_content)
    {
        $this->set_default_property(self :: PROPERTY_MAIL_CONTENT, $mail_content);
    }

    /**
     * Returns the mail_content of this SurveyPublicationMail.
     * @return the mail_content.
     */
    function get_mail_content()
    {
        return $this->get_default_property(self :: PROPERTY_MAIL_CONTENT);
    }

    /**
     * Returns the from_address of this SurveyPublicationMail.
     * @return the from_address.
     */
    function get_from_address()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_ADDRES);
    }

    /**
     * Sets the from_address of this SurveyPublicationMail.
     * @param from_address_name
     */
    function set_from_address($from_address)
    {
        $this->set_default_property(self :: PROPERTY_FROM_ADDRES, $from_address);
    }
	
	function get_from_address_name()
    {
        return $this->get_default_property(self :: PROPERTY_FROM_ADDRES_NAME);
    }

    /**
     * Sets the from_address of this SurveyPublicationMail.
     * @param from_address_name
     */
    function set_from_address_name($from_address_name)
    {
        $this->set_default_property(self :: PROPERTY_FROM_ADDRES_NAME, $from_address_name);
    }
    
    
/**
     * Returns the reply_address of this SurveyPublicationMail.
     * @return the reply_address.
     */
    function get_reply_address()
    {
        return $this->get_default_property(self :: PROPERTY_REPLY_ADDRES);
    }

    /**
     * Sets the reply_address of this SurveyPublicationMail.
     * @param reply_address
     */
    function set_reply_address($reply_address)
    {
        $this->set_default_property(self :: PROPERTY_REPLY_ADDRES, $reply_address);
    }

/**
     * Returns the reply_address of this SurveyPublicationMail.
     * @return the reply_address_name.
     */
    function get_reply_address_name()
    {
        return $this->get_default_property(self :: PROPERTY_REPLY_ADDRES_NAME);
    }

    /**
     * Sets the reply_address of this SurveyPublicationMail.
     * @param reply_address_name
     */
    function set_reply_address_name($reply_address_name)
    {
        $this->set_default_property(self :: PROPERTY_REPLY_ADDRES_NAME, $reply_address_name);
    }
    
    
    
    /**
     * Returns the sender_user_id of this SurveyPublicationMail.
     * @return the sender_user_id.
     */
    function get_sender_user_id()
    {
        return $this->get_default_property(self :: PROPERTY_SENDER_USER_ID);
    }

    /**
     * Sets the sender_user_id of this SurveyPublicationMail.
     * @param sender_user_id
     */
    function set_sender_user_id($sender_user_id)
    {
        $this->set_default_property(self :: PROPERTY_SENDER_USER_ID, $sender_user_id);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }

}

?>
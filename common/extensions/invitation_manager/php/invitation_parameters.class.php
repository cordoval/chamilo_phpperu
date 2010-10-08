<?php
class InvitationParameters
{
    private $valid_email_regex = '/^((\"[^\"\f\n\r\t\v\b]+\")|([\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+(\.[\w\!\#\$\%\&\'\*\+\-\~\/\^\`\|\{\}]+)*))@((\[(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))\])|(((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9]))\.((25[0-5])|(2[0-4][0-9])|([0-1]?[0-9]?[0-9])))|((([A-Za-z0-9\-])+\.)+[A-Za-z\-]+))$/';

    /**
     * @var array
     */
    private $emails = array();

    /**
     * @var array
     */
    private $invalid_emails = array();

    /**
     * @var array
     */
    private $properties = array();

    function InvitationParameters()
    {
    }

    /**
     * @param array $emails
     */
    function set_emails(array $emails)
    {
        $this->emails = $emails;
    }

    /**
     * @return array:
     */
    function get_emails()
    {
        return $this->emails;
    }

    /**
     * @param string $email
     */
    function add_email($email)
    {
        if (!in_array($email, $this->emails))
        {
            $this->emails[] = $email;
        }
    }

    /**
     * @param array $properties
     */
    function set_properties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @return array:
     */
    function get_properties()
    {
        return $this->properties;
    }

    /**
     * @param string $emails
     */
    function set_emails_from_string($emails)
    {
        $emails = explode(',', $emails);

        foreach ($emails as $email)
        {
            $email = trim($email);

            if (preg_match($this->valid_email_regex . 'D', $email))
            {
                $this->add_email($email);
            }
            else
            {
                $this->invalid_emails[] = $email;
            }
        }
    }
}
?>
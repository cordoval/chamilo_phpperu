<?php
/**
 * $Id: personal_message_publication.class.php 203 2009-11-13 12:46:38Z chellee $
 * @package application.personal_messenger
 * @author Hans De Bisschop
 * @author Dieter De Neef
 */

/**
 *	This class represents a personal message.
 *
 *	personal message (PM) objects have a number of default properties:
 *	- id: the numeric ID of the PM;
 *	- content_object: the numeric object ID of the PM (from the repository);
 *	- status: the status of the PM: read/unread/...;
 *	- recipient: the recipient of the PM;
 *	- publisher: the publisher of the PM;
 *	- published: the date when the PM was "posted";
 *	@author Hans de Bisschop
 *	@author Dieter De Neef
 */
class PersonalMessagePublication extends DataClass
{
    const CLASS_NAME = __CLASS__;
    const TABLE_NAME = 'publication';
    
    const PROPERTY_PERSONAL_MESSAGE = 'personal_message_id';
    const PROPERTY_STATUS = 'status';
    const PROPERTY_USER = 'user_id';
    const PROPERTY_SENDER = 'sender_id';
    const PROPERTY_RECIPIENT = 'recipient_id';
    const PROPERTY_PUBLISHED = 'published';

    /**
     * Get the default properties of all users.
     * @return array The property names.
     */
    static function get_default_property_names()
    {
        return parent :: get_default_property_names(array(self :: PROPERTY_PERSONAL_MESSAGE, self :: PROPERTY_STATUS, self :: PROPERTY_USER, self :: PROPERTY_SENDER, self :: PROPERTY_RECIPIENT, self :: PROPERTY_PUBLISHED));
    }

    /**
     * inherited
     */
    function get_data_manager()
    {
        return PersonalMessengerDataManager :: get_instance();
    }

    /**
     * Returns the learning object id from this PMP object
     * @return int The personal message ID
     */
    function get_personal_message()
    {
        return $this->get_default_property(self :: PROPERTY_PERSONAL_MESSAGE);
    }

    /**
     * Returns the status of this PMP object
     * @return int the status
     */
    function get_status()
    {
        return $this->get_default_property(self :: PROPERTY_STATUS);
    }

    /**
     * Returns the user of this PMP object
     * @return int the user
     */
    function get_user()
    {
        return $this->get_default_property(self :: PROPERTY_USER);
    }

    /**
     * Returns the sender of this PMP object
     * @return int the sender
     */
    function get_sender()
    {
        return $this->get_default_property(self :: PROPERTY_SENDER);
    }

    /**
     * Returns the recipient of this PMP object
     * @return int the recipient
     */
    function get_recipient()
    {
        return $this->get_default_property(self :: PROPERTY_RECIPIENT);
    }

    /**
     * Returns the published timestamp of this PMP object
     * @return Timestamp the published date
     */
    function get_published()
    {
        return $this->get_default_property(self :: PROPERTY_PUBLISHED);
    }

    /**
     * Sets the learning object id of this PMP.
     * @param Int $id the personal message ID.
     */
    function set_personal_message($id)
    {
        $this->set_default_property(self :: PROPERTY_PERSONAL_MESSAGE, $id);
    }

    /**
     * Sets the status of this PMP.
     * @param int $status the Status.
     */
    function set_status($status)
    {
        $this->set_default_property(self :: PROPERTY_STATUS, $status);
    }

    /**
     * Sets the user of this PMP.
     * @param int $user the User.
     */
    function set_user($user)
    {
        $this->set_default_property(self :: PROPERTY_USER, $user);
    }

    /**
     * Sets the sender of this PMP.
     * @param int $sender the Sender.
     */
    function set_sender($sender)
    {
        $this->set_default_property(self :: PROPERTY_SENDER, $sender);
    }

    /**
     * Sets the recipient of this PMP.
     * @param int $recipient the user_id of the recipient.
     */
    function set_recipient($recipient)
    {
        $this->set_default_property(self :: PROPERTY_RECIPIENT, $recipient);
    }

    /**
     * Sets the published date of this PM.
     * @param int $published the timestamp of the published date.
     */
    function set_published($published)
    {
        $this->set_default_property(self :: PROPERTY_PUBLISHED, $published);
    }

    function get_publication_object()
    {
        $rdm = RepositoryDataManager :: get_instance();
        return $rdm->retrieve_content_object($this->get_personal_message());
    }

    function get_publication_sender()
    {
        return $this->get_publication_user($this->get_sender());
    }

    function get_publication_recipient()
    {
        return $this->get_publication_user($this->get_recipient());
    }

    function get_publication_user($user_id)
    {
        $udm = UserDataManager :: get_instance();
        return $udm->retrieve_user($user_id);
    }

    /**
     * Instructs the data manager to create the personal message publication, making it
     * persistent. Also assigns a unique ID to the publication and sets
     * the publication's creation date to the current time.
     * @return boolean True if creation succeeded, false otherwise.
     */
    function create()
    {
        $now = time();
        $this->set_published($now);
        $pmdm = PersonalMessengerDataManager :: get_instance();
        $id = $pmdm->get_next_personal_message_publication_id();
        $this->set_id($id);
        return $pmdm->create_personal_message_publication($this);
    }

    static function get_table_name()
    {
        return self :: TABLE_NAME;
    }
}
?>

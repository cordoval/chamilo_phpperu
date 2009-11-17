<?php
require_once dirname(__FILE__) . '/forum_post.class.php';
/**
 * @package repository.learningobject
 * @subpackage forum
 */
class ForumPostForm extends ContentObjectForm
{

    function create_content_object()
    {
        $object = new ForumPost();
        $this->set_content_object($object);
        return parent :: create_content_object();
    }

    function build_creation_form()
    {
        parent :: build_creation_form();
        //$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    /*$this->build_form();*/
    //$this->addElement('category');
    }

    function build_editing_form()
    {
        parent :: build_editing_form();
        //$this->addElement('category', Translation :: get(get_class($this) .'Properties'));
    /*$this->build_form();*/
    //$this->addElement('category');
    }

    /**
     * Override the regular form by adding a selection for email notifications
     * A new field has been added to the forum_topic table to store emails of users
     * who need to be notified when new messages are posted. 
     */
    private function build_form()
    {
        $this->add_select(ForumPost :: PROPERTY_NOTIFICATION, "Notification? ", array(ForumPost :: NOTIFY_NONE => 'None', ForumPost :: NOTIFY_TOPIC => 'Notify me of any replies in this thread'), false);
    
    }

}
?>

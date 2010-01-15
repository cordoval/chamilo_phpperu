<?php
/**
 * $Id: announcement_distribution_form.class.php 194 2009-11-13 11:54:13Z chellee $
 * @package application.lib.distribute.forms
 */
require_once dirname(__FILE__) . '/../announcement_distribution.class.php';

/**
 * This class describes the form for a AnnouncementPublication object.
 * @author Hans De Bisschop
 **/
class AnnouncementDistributionForm extends FormValidator
{
    /**#@+
     * Constant defining a form parameter
     */

    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;

    /**#@-*/
    /**
     * The learning object that will be published
     */
    private $content_object;
    /**
     * The publication that will be changed (when using this form to edit a
     * publication)
     */
    private $form_user;

    private $form_type;

    /**
     * Creates a new learning object publication form.
     * @param ContentObject The learning object that will be published
     * @param string $tool The tool in which the object will be published
     * @param boolean $email_option Add option in form to send the learning
     * object by email to the receivers
     */
    function AnnouncementDistributionForm($form_type, $content_object, $form_user, $action)
    {
        parent :: __construct('publish', 'post', $action);
        $this->form_type = $form_type;
        $this->content_object = $content_object;
        $this->form_user = $form_user;

        switch ($this->form_type)
        {
            case self :: TYPE_SINGLE :
                $this->build_single_form();
                break;
            case self :: TYPE_MULTI :
                $this->build_multi_form();
                break;
        }
        $this->add_footer();
        $this->setDefaults();
    }

    /**
     * Sets the default values of the form.
     *
     * By default the publication is for everybody who has access to the tool
     * and the publication will be available forever.
     */
    function setDefaults()
    {
        $defaults = array();
        parent :: setDefaults($defaults);
    }

    function build_single_form()
    {
        $this->build_form();
    }

    function build_multi_form()
    {
        $this->build_form();
        $this->addElement('hidden', 'ids', serialize($this->content_object));
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    function build_form()
    {
        $shares = array();

        $url = Path :: get(WEB_PATH) . 'common/xml_feeds/xml_user_group_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('ShareWith');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $hidden = false;
        $elem = $this->addElement('user_group_finder', 'recipients', Translation :: get('SendTo'), $url, $locale, $shares);
        $elem->excludeElements(array('user_' . $this->form_user->get_id()));
        $elem->setDefaultCollapsed(false);
    }

    function add_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        //$this->addElement('submit', 'submit', Translation :: get('Ok'));
    }

    /**
     * Creates a learning object publication using the values from the form.
     * @return ContentObjectPublication The new publication
     */
    function create_announcement_distribution()
    {
        $values = $this->exportValues();
        $recipients = $values['recipients'];

        $pub = new AnnouncementDistribution();
        $pub->set_announcement($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_target_users($recipients['user']);
        $pub->set_target_groups($recipients['group']);

        if ($pub->create())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_announcement_distributions()
    {
        $values = $this->exportValues();
        $ids = unserialize($values['ids']);
        $recipients = $values['recipients'];

        foreach ($ids as $id)
        {
            $pub = new AnnouncementDistribution();
            $pub->set_announcement($id);
            $pub->set_publisher($this->form_user->get_id());
            $pub->set_published(time());
            $pub->set_target_users($recipients['user']);
            $pub->set_target_groups($recipients['group']);

            if (! $pub->create())
            {
                return false;
            }
        }
        return true;
    }
}
?>
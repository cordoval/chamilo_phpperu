<?php
/**
 * $Id: phrases_publication_form.class.php 201 2009-11-13 12:34:51Z chellee $
 * @package application.personal_calendar
 */

require_once dirname(__FILE__) . '/../phrases_publication.class.php';
require_once Path :: get_plugin_path() . 'html2text/class.html2text.inc';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class PhrasesPublicationForm extends FormValidator
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
    private $publication;
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
    function PhrasesPublicationForm($form_type, $content_object, $form_user, $action)
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
        $this->addElement('category', Translation :: get('Properties'));
        $this->addElement('select', PhrasesPublication :: PROPERTY_MASTERY_LEVEL_ID, Translation :: get('Difficulty'), $this->get_mastery_levels());
        $this->addElement('select', PhrasesPublication :: PROPERTY_LANGUAGE_ID, Translation :: get('Language'), $this->get_languages());
        $this->addElement('category');
    }

    function get_mastery_levels()
    {
        $levels = array();

        $mastery_levels = PhrasesDataManager::get_instance()->retrieve_phrases_mastery_levels(null, new ObjectTableOrder(PhrasesMasteryLevel::PROPERTY_DISPLAY_ORDER, SORT_ASC));

        while($mastery_level = $mastery_levels->next_result())
        {
            $levels[$mastery_level->get_id()] = Translation :: get($mastery_level->get_level());
        }

        return $levels;
    }

    function get_languages()
    {
        $languages = AdminDataManager :: get_languages(false);
        asort($languages);
        return $languages;
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
    function create_content_object_publication()
    {
        $values = $this->exportValues();

        $pub = new PhrasesPublication();
        $pub->set_content_object_id($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_mastery_level_id($values[PhrasesPublication :: PROPERTY_MASTERY_LEVEL_ID]);
        $pub->set_language_id($values[PhrasesPublication :: PROPERTY_LANGUAGE_ID]);
        $pub->set_category_id(0);

        if ($pub->create())
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    function create_content_object_publications()
    {
        $values = $this->exportValues();

        $ids = unserialize($values['ids']);

        foreach ($ids as $id)
        {
            $pub = new PhrasesPublication();
            $pub->set_content_object_id($id);
            $pub->set_publisher($this->form_user->get_id());
            $pub->set_published(time());
            $pub->set_mastery_level_id($values[PhrasesPublication :: PROPERTY_MASTERY_LEVEL_ID]);
            $pub->set_language_id($values[PhrasesPublication :: PROPERTY_LANGUAGE_ID]);
            $pub->set_category_id(0);

            if (! $pub->create())
            {
                return false;
            }
        }
        return true;
    }

    function set_publication($publication)
    {
        $this->publication = $publication;
        $this->addElement('hidden', 'pid');
        $this->addElement('hidden', 'action');
        $defaults['action'] = 'edit';
        $defaults['pid'] = $publication->get_id();
        $defaults[PhrasesPublication::PROPERTY_MASTERY_LEVEL_ID] = $publication->get_mastery_level_id();
        $defaults[PhrasesPublication::PROPERTY_LANGUAGE_ID] = $publication->get_language_id();

        parent :: setDefaults($defaults);
    }

    function update_content_object_publication()
    {
        $values = $this->exportValues();

        $pub = $this->publication;
        $pub->set_mastery_level_id($values[PhrasesPublication :: PROPERTY_MASTERY_LEVEL_ID]);
        $pub->set_language_id($values[PhrasesPublication :: PROPERTY_LANGUAGE_ID]);
        $pub->set_category_id(0);
        $pub->update();
        return $pub;
    }
}
?>
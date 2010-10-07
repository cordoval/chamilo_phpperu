<?php
/**
 * $Id: gutenberg_publication_form.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.gutenberg.forms
 */
require_once WebApplication :: get_application_class_lib_path('gutenberg') . 'gutenberg_publication.class.php';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class GutenbergPublicationForm extends FormValidator
{
    /**#@+
     * Constant defining a form parameter
     */
    
    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;
    
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';
    const PARAM_HIDDEN = 'hidden';
    
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
    
    private $form_user;
    
    private $form_type;

    /**
     * Creates a new learning object publication form.
     * @param ContentObject The learning object that will be published
     * @param string $tool The tool in which the object will be published
     * @param boolean $email_option Add option in form to send the learning
     * object by email to the receivers
     */
    function GutenbergPublicationForm($form_type, $content_object, $form_user, $action)
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
     * Sets the publication. Use this function if you're using this form to
     * change the settings of a learning object publication.
     * @param ContentObjectPublication $publication
     */
    function set_publication($publication)
    {
        $this->publication = $publication;
        $this->addElement('hidden', 'pid');
        $this->addElement('hidden', 'action');
        $defaults['action'] = 'edit';
        $defaults['pid'] = $publication->get_id();
        $defaults['from_date'] = $publication->get_from_date();
        $defaults['to_date'] = $publication->get_to_date();
        if ($defaults['from_date'] != 0)
        {
            $defaults['forever'] = 0;
        }
        $defaults['hidden'] = $publication->is_hidden();
        
        parent :: setDefaults($defaults);
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
        $defaults[self :: PARAM_FOREVER] = 1;
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
        $this->add_forever_or_timewindow();
        $this->addElement('checkbox', self :: PARAM_HIDDEN, Translation :: get('Hidden'));
    }

    function add_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Creates a learning object publication using the values from the form.
     * @return ContentObjectPublication The new publication
     */
    function create_content_object_publication()
    {
        $values = $this->exportValues();
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[self :: PARAM_HIDDEN] ? 1 : 0);
        
        $pub = new GutenbergPublication();
        $pub->set_content_object($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_hidden($hidden);
        
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
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[self :: PARAM_HIDDEN] ? 1 : 0);
        
        $ids = unserialize($values['ids']);
        
        foreach ($ids as $id)
        {
            $pub = new GutenbergPublication();
            $pub->set_content_object($id);
            $pub->set_publisher($this->form_user->get_id());
            $pub->set_published(time());
            $pub->set_from_date($from);
            $pub->set_to_date($to);
            $pub->set_hidden($hidden);
            
            if (! $pub->create())
            {
                return false;
            }
        }
        return true;
    }

    function update_content_object_publication()
    {
        $values = $this->exportValues();
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[self :: PARAM_HIDDEN] ? 1 : 0);
        
        $pub = $this->publication;
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_hidden($hidden);
        return $pub->update();
    }
}
?>
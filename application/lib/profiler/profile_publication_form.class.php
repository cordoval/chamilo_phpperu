<?php
/**
 * $Id: profile_publication_form.class.php 212 2009-11-13 13:38:35Z chellee $
 * @package application.profiler
 */
require_once dirname(__FILE__) . '/profile_publication.class.php';
require_once Path :: get_plugin_path() . 'html2text/class.html2text.inc';
/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class ProfilePublicationForm extends FormValidator
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
    function ProfilePublicationForm($form_type, $content_object, $form_user, $action)
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
    
    private $categories;
    private $level = 1;

    function get_categories($parent_id)
    {
        $condition = new EqualityCondition(ProfilerCategory :: PROPERTY_PARENT, $parent_id);
        
        $cats = ProfilerDataManager :: get_instance()->retrieve_categories($condition);
        while ($cat = $cats->next_result())
        {
            $this->categories[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
            $this->level ++;
            $this->get_categories($cat->get_id());
            $this->level --;
        }
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    function build_form()
    {
        $this->categories[0] = Translation :: get('Root');
        $this->get_categories(0);
        
        //if(count($this->categories) > 1)
        {
            // More than one category -> let user select one
            $this->addElement('select', ProfilePublication :: PROPERTY_CATEGORY, Translation :: get('Category'), $this->categories);
        }
        /*else
		{
			// Only root category -> store object in root category
			$this->addElement('hidden',ProfilePublication :: PROPERTY_CATEGORY,0);
		}*/
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
        
        $pub = new ProfilePublication();
        $pub->set_profile($this->content_object->get_id());
        $pub->set_publisher($this->form_user->get_id());
        $pub->set_published(time());
        $pub->set_category($values[ProfilePublication :: PROPERTY_CATEGORY]);
        
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
            $pub = new ProfilePublication();
            $pub->set_profile($id);
            $pub->set_publisher($this->form_user->get_id());
            $pub->set_published(time());
            $pub->set_category($values[ProfilePublication :: PROPERTY_CATEGORY]);
            
            if (! $pub->create())
            {
                return false;
            }
        }
        return true;
    }

    function set_profile_publication($profile_publication)
    {
        $this->profile_publication = $profile_publication;
        $this->addElement('hidden', 'prid');
        $this->addElement('hidden', 'action');
        $defaults['action'] = 'edit';
        $defaults['prid'] = $profile_publication->get_id();
        $defaults[ProfilePublication :: PROPERTY_CATEGORY] = $profile_publication->get_category();
        
        parent :: setDefaults($defaults);
    }

    function update_content_object_publication()
    {
        $values = $this->exportValues();
        
        $pub = $this->profile_publication;
        $pub->set_category($values[ProfilePublication :: PROPERTY_CATEGORY]);
        
        if ($pub->update())
        {
            return true;
        }
        else
        {
            return false;
        }
    }
}
?>
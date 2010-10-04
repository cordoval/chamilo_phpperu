<?php
/**
 * $Id: buddy_list_category_form.class.php 211 2009-11-13 13:28:39Z vanpouckesven $
 * @package user.lib.forms
 */

class BuddyListCategoryForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'BuddyListCategoryUpdated';
    const RESULT_ERROR = 'BuddyListCategoryUpdateFailed';
    
    private $category;
    private $user;
    private $form_type;
    private $manager;

    /**
     * Creates a new LanguageForm
     */
    function BuddyListCategoryForm($form_type, $action, $category, $user, $manager)
    {
        parent :: __construct('category_form', 'post', $action);
        
        $this->category = $category;
        $this->user = $user;
        $this->form_type = $form_type;
        $this->manager = $manager;
        
        $this->build_header();
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        else
        {
            $this->build_creation_form();
        }
        
        $this->build_footer();
        
        $this->setDefaults();
    }

    function build_header()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
    }

    function build_footer()
    {
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        // Submit button
        //$this->addElement('submit', 'submit', 'OK');
        

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function add_title_field($number = null)
    {
        $element = $this->createElement('text', BuddyListCategory :: PROPERTY_TITLE . $number, Translation :: get('Name'), array("size" => "50"));
        //$this->addRule(BuddyListCategory :: PROPERTY_TITLE . $number, Translation :: get('ThisFieldIsRequired'), 'required');
        return $element;
    }

    /**
     * Creates a new basic form
     */
    function build_creation_form()
    {
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }
        
        if (! isset($_SESSION['mc_number_of_options']))
        {
            $_SESSION['mc_number_of_options'] = 3;
        }
        
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_skip_options'][] = $indexes[0];
        }
        
        $number_of_options = intval($_SESSION['mc_number_of_options']);
        
        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->add_title_field($option_number);
                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $group[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_list_remove.png', array('style="border: 0px;"'));
                }
                $this->addGroup($group, BuddyListCategory :: PROPERTY_TITLE . $option_number, Translation :: get('BuddyListCategoryName'), '', false);
                $this->addRule(BuddyListCategory :: PROPERTY_TITLE . $option_number, Translation :: get('ThisFieldIsRequired'), 'required');
            }
        }
        
        $this->addElement('image', 'add[]', Theme :: get_common_image_path() . 'action_list_add.png', array('style="border: 0px;"'));
    
    }

    function validate()
    {
        if (isset($_POST['add']) || isset($_POST['remove']))
        {
            return false;
        }
        return parent :: validate();
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
        $this->addElement($this->add_title_field());
        $this->addElement('hidden', BuddyListCategory :: PROPERTY_ID);
    }

    function create_category()
    {
        $values = $this->exportValues();
        //dump($values);
        

        $result = true;
        
        foreach ($values as $key => $value)
        {
            if (strpos($key, 'title') !== false)
            {
                $category = new BuddyListCategory();
                $category->set_title($value);
                $category->set_user_id($this->user->get_id());
                
                $conditions[] = new EqualityCondition(BuddyListCategory :: PROPERTY_TITLE, $category->get_title());
                $conditions[] = new EqualityCondition(BuddyListCategory :: PROPERTY_USER_ID, $category->get_user_id());
                $condition = new AndCondition($conditions);
                
                $cats = UserDataManager :: get_instance()->retrieve_buddy_list_categories($condition);
                
                if ($cats->size() > 0)
                {
                    $result = false;
                }
                else
                {
                    $result &= $category->create();
                }
            }
        }
        return $result;
    }

    function update_category()
    {
        $category = $this->category;
        $category->set_title($this->exportValue(BuddyListCategory :: PROPERTY_TITLE));
        
        $conditions[] = new EqualityCondition(BuddyListCategory :: PROPERTY_TITLE, $category->get_title());
        $conditions[] = new EqualityCondition(BuddyListCategory :: PROPERTY_USER_ID, $category->get_user_id());
        $condition = new AndCondition($conditions);
        
        $cats = UserDataManager :: get_instance()->retrieve_buddy_list_categories($condition);
        
        if ($cats->size() > 0)
        {
            return false;
        }
        
        return $category->update();
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $category = $this->category;
        $defaults[BuddyListCategory :: PROPERTY_ID] = $category->get_id();
        $defaults[BuddyListCategory :: PROPERTY_TITLE] = $category->get_title();
        parent :: setDefaults($defaults);
    }
}
?>
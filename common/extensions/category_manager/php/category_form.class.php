<?php
/**
 * $Id: category_form.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.category_manager
 */
require_once dirname(__FILE__) . '/platform_category.class.php';

class CategoryForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'CategoryUpdated';
    const RESULT_ERROR = 'CategoryUpdateFailed';
    
    private $category;
    private $user;
    private $form_type;
    private $manager;

    /**
     * Creates a new LanguageForm
     */
    function CategoryForm($form_type, $action, $category, $user, $manager)
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
        
        $this->setDefaults();
    }

    function build_header()
    {
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
    }

    function build_footer($action_name)
    {
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        // Submit button
        //$this->addElement('submit', 'submit', 'OK');
        
        $buttons[] = $this->createElement('style_submit_button', 'create', Translation :: get($action_name), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $this->addElement('html',  ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_LIB_PATH) . 'javascript/category_form.js'));
    }

    function add_name_field($number = null)
    {
        $element = $this->createElement('text', PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('Name'), array("size" => "50"));
        //$this->addRule(PlatformCategory :: PROPERTY_NAME . $number, Translation :: get('ThisFieldIsRequired'), 'required');
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
            $_SESSION['mc_number_of_options'] = 1;
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
                $group[] = $this->add_name_field($option_number);
                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $group[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_list_remove.png', array('style="border: 0px;"'));
                }
                $this->addGroup($group, PlatformCategory :: PROPERTY_NAME . $option_number, Translation :: get('CategoryName'), '', false);
                $this->addRule(PlatformCategory :: PROPERTY_NAME . $option_number, Translation :: get('ThisFieldIsRequired'), 'required');
            }
        }
        
        $this->addElement('image', 'add[]', Theme :: get_common_image_path() . 'action_list_add.png', array('style="border: 0px;"'));
        $this->build_footer('Create');
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
        $this->addElement($this->add_name_field());
        $this->addElement('hidden', PlatformCategory :: PROPERTY_ID);
        $this->build_footer('Update');
    }

    function create_category()
    {
        $values = $this->exportValues();
        //dump($values);
        

        $result = true;
        
        foreach ($values as $key => $value)
        {
            if (strpos($key, 'name') !== false)
            {
                $category = $this->manager->get_category();
                $category->set_name($value);
                $category->set_parent($this->category->get_parent());
                
                $conditions = array();
                $conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_NAME, $category->get_name());
                $conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $category->get_parent());
                $condition = new AndCondition($conditions);
                $cats = $this->manager->count_categories($condition);
                
                if ($cats > 0)
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
        $category->set_name($this->exportValue(PlatformCategory :: PROPERTY_NAME));
        
        $conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_NAME, $category->get_name());
        $conditions[] = new EqualityCondition(PlatformCategory :: PROPERTY_PARENT, $category->get_parent());
        $condition = new AndCondition($conditions);
        $cats = $this->manager->count_categories($condition);
        
        if ($cats > 0)
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
        $defaults[PlatformCategory :: PROPERTY_ID] = $category->get_id();
        $defaults[PlatformCategory :: PROPERTY_NAME] = $category->get_name();
        parent :: setDefaults($defaults);
    }
}
?>
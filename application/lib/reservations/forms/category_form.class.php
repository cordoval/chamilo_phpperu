<?php
/**
 * $Id: category_form.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.forms
 */
require_once dirname(__FILE__) . '/../category.class.php';
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';

class CategoryForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'CategoryUpdated';
    const RESULT_ERROR = 'CategoryUpdateFailed';
    
    private $category;
    private $user;
    private $form_type;

    /**
     * Creates a new LanguageForm
     */
    function CategoryForm($form_type, $action, $category, $user)
    {
        parent :: __construct('category_form', 'post', $action);
        
        $this->category = $category;
        $this->user = $user;
        $this->form_type = $form_type;
        
        $this->build_basic_form();
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        
        $this->setDefaults();
    }

    /**
     * Creates a new basic form
     */
    function build_basic_form()
    {
        $this->addElement('html', '<div style="float: left;width: 100%;">');
        
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
        
        // Name
        $this->addElement('text', Category :: PROPERTY_NAME, Translation :: get('Name'));
        $this->addRule(Category :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        
        $this->addElement('checkbox', Category :: PROPERTY_POOL, Translation :: get('Pool'));
        //$this->addRule(Category :: PROPERTY_POOL, Translation :: get('ThisFieldIsRequired'), 'required');
        

        // Submit button
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div></div>');
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
        $this->addElement('hidden', Category :: PROPERTY_ID);
    }

    function create_category()
    {
        $category = $this->category;
        $category->set_name($this->exportValue(Category :: PROPERTY_NAME));
        $pool = $this->exportValue(Category :: PROPERTY_POOL);
        $category->set_pool($pool ? $pool : 0);
        
        $succes = $category->create();
        
        if ($succes)
            Events :: trigger_event('create_category', 'reservations', array('target_id' => $category->get_id(), 'user_id' => $this->user->get_id()));
        
        return $succes;
    }

    function update_category()
    {
        $category = $this->category;
        $category->set_name($this->exportValue(Category :: PROPERTY_NAME));
        $pool = $this->exportValue(Category :: PROPERTY_POOL);
        $category->set_pool($pool ? $pool : 0);
        $succes = $category->update();
        
        if ($succes)
            Events :: trigger_event('update_category', 'reservations', array('target_id' => $category->get_id(), 'user_id' => $this->user->get_id()));
        
        return $succes;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $category = $this->category;
        $defaults[Category :: PROPERTY_ID] = $category->get_id();
        $defaults[Category :: PROPERTY_NAME] = $category->get_name();
        $defaults[Category :: PROPERTY_POOL] = $category->get_pool();
        parent :: setDefaults($defaults);
    }
}
?>
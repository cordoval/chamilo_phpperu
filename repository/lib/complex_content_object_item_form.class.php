<?php
/**
 * $Id: complex_content_object_item_form.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib
 */

/**
 * A form to create and edit a ComplexComplexContentObjectItemItem.
 */
abstract class ComplexContentObjectItemForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'ObjectUpdated';
    const RESULT_ERROR = 'ObjectUpdateFailed';
    
    private $complex_content_object_item;
    
    protected $form_type;

    /**
     * Constructor.
     * @param int $form_type The form type; either
     *                       ComplexComplexContentObjectItemItemForm :: TYPE_CREATE or
     *                       ComplexComplexContentObjectItemItemForm :: TYPE_EDIT.
     * @param ComplexComplexContentObjectItemItem $content_object_item The object to create or update.
     *                                        May be an AbstractComplexContentObjectItem
     *                                        upon creation.
     * @param string $form_name The name to use in the form tag.
     * @param string $method The method to use ('post' or 'get').
     * @param string $action The URL to which the form should be submitted.
     */
    protected function __construct($form_type, $complex_content_object_item, $form_name, $method = 'post', $action = null)
    {
        parent :: __construct($form_name, $method, $action);
        $this->form_type = $form_type;
        $this->complex_content_object_item = $complex_content_object_item;
        
        if ($this->form_type == self :: TYPE_EDIT)
        {
            $this->build_editing_form();
        }
        elseif ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_creation_form();
        }
        
        $this->add_progress_bar(2);
        
        $this->setDefaults();
    }

    function get_complex_content_object_item()
    {
        return $this->complex_content_object_item;
    }

    function set_complex_content_object_item($cloi)
    {
        $this->complex_content_object_item = $cloi;
    }

    protected function get_form_type()
    {
        return $this->form_type;
    }

    /**
     * Builds a form to create a new learning object.
     */
    protected function build_creation_form()
    {
        $this->build_basic_form();
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds a form to edit a learning object.
     */
    protected function build_editing_form()
    {
        $this->build_basic_form();
        $this->addElement('hidden', ComplexContentObjectItem :: PROPERTY_ID);
        
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update'), array('class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds a form to create or edit a learning object. Creates fields for
     * default learning object properties. The result of this function is equal
     * to build_creation_form()'s, but that one may be overridden to extend the
     * form.
     */
    private function build_basic_form()
    {
    
    }

    /**
     * Sets default values. Traditionally, you will want to extend this method
     * so it sets default for your learning object type's additional
     * properties.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $cloi = $this->complex_content_object_item;
        $defaults[ComplexContentObjectItem :: PROPERTY_ID] = $cloi->get_id();
        parent :: setDefaults($defaults);
    }

    function set_values($defaults)
    {
        parent :: setDefaults($defaults);
    }

    /**
     * Creates a complex learning object item from the submitted form values. Traditionally,
     * you override this method to ensure that the form's complex learning object item is
     * set to the object that is to be created, and call the super method.
     * @return ComplexContentObjectItem The newly created complex learning object item.
     */
    function create_complex_content_object_item()
    {
        $cloi = $this->complex_content_object_item;
        return $cloi->create();
    }

    function create_cloi_from_values($values)
    {
        $cloi = $this->complex_content_object_item;
        return $cloi->create();
    }

    /**
     * Updates a complex learning object item with the submitted form values. Traditionally,
     * you override this method to first set values for the necessary
     * additional complex learning object item properties, and then call the super method.
     * @return boolean True if the update succeeded, false otherwise.
     */
    function update_complex_content_object_item()
    {
        $cloi = $this->complex_content_object_item;
        $values = $this->exportValues();
        if ($values[ComplexContentObjectItem :: PROPERTY_ID])
            $cloi->set_id($values[ComplexContentObjectItem :: PROPERTY_ID]);
        return $cloi->update();
    }

    /**
     * Creates a form object to manage a complex learning object item.
     * @param int $form_type The form type; either
     *                       ComplexComplexContentObjectItemItemForm :: TYPE_CREATE or
     *                       ComplexComplexContentObjectItemItemForm :: TYPE_EDIT.
     * @param ComplexContentObjectItem $complex_content_object_item The object to create or update.
     *                                        May be an ComplexContentObjectItem
     *                                        upon creation.
     * @param string $form_name The name to use in the form tag.
     * @param string $method The method to use ('post' or 'get').
     * @param string $action The URL to which the form should be submitted.
     */
    static function factory($form_type, $complex_content_object_item, $form_name, $method = 'post', $action = null)
    {
        if (! $complex_content_object_item->is_extended())
            return null;
        
        $rdm = RepositoryDataManager :: get_instance();
        
        $ref = $complex_content_object_item->get_ref();
        
        $type = $rdm->determine_content_object_type($ref);
        
        $class = 'Complex' . Utilities :: underscores_to_camelcase($type) . 'Form';
        $file = dirname(__FILE__) . '/content_object/' . $type . '/complex_' . $type . '_form.class.php';
        
        require_once $file;
        return new $class($form_type, $complex_content_object_item, $form_name, $method, $action);
    }

    static function factory_with_type($form_type, $type, $complex_content_object_item, $form_name, $method = 'post', $action = null)
    {
        if (! $complex_content_object_item->is_extended())
            return null;
        
        $class = 'Complex' . Utilities :: underscores_to_camelcase($type) . 'Form';
        $file = dirname(__FILE__) . '/content_object/' . $type . '/complex_' . $type . '_form.class.php';
        
        require_once $file;
        return new $class($form_type, $complex_content_object_item, $form_name, $method, $action);
    }

    function get_path($path_type)
    {
        return Path :: get($path_type);
    }

    function get_elements()
    {
        return array();
    }
    
	function get_default_values()
    {
        return array();
    }
}
?>

<?php
/**
 * $Id: credit_form.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.forms
 */
require_once dirname(__FILE__) . '/../category.class.php';
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';

class CreditForm extends FormValidator
{
    
    const RESULT_SUCCESS = 'CreditUpdated';
    const RESULT_ERROR = 'CreditUpdateFailed';
    
    private $dm;

    /**
     * Creates a new LanguageForm
     */
    function CreditForm($action)
    {
        parent :: __construct('credit_form', 'post', $action);
        $this->build_basic_form();
        $this->dm = ReservationsDataManager :: get_instance();
    }

    /**
     * Creates a new basic form
     */
    function build_basic_form()
    {
        
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
        
        // Name
        $this->addElement('text', 'credits', Translation :: get('Credits'));
        $this->addRule('credits', Translation :: get('ThisFieldIsRequired'), 'required');
        
        // Submit button
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $this->addElement('html', '<div style="clear: both;"></div></div>');
    }

    function set_credits_for_category($category, $credits = null)
    {
        if ($credits == null)
            $credits = $this->exportValue('credits');
        
        $bool = true;
        
        $items = $this->dm->retrieve_items(new EqualityCondition(Item :: PROPERTY_CATEGORY, $category));
        while ($item = $items->next_result())
        {
            $item->set_credits($credits);
            $bool = $bool & $item->update();
        }
        
        $categories = $this->dm->retrieve_categories(new EqualityCondition(Category :: PROPERTY_PARENT, $category));
        while ($category = $categories->next_result())
        {
            $bool = $bool & $this->set_credits_for_category($category->get_id(), $credits);
        }
        
        return $bool;
    }
}
?>
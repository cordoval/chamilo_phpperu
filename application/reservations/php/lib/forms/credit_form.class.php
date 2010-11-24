<?php

namespace application\reservations;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\EqualityCondition;
use common\libraries\Utilities;
/**
 * $Id: credit_form.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.forms
 */
class CreditForm extends FormValidator
{
    
    const RESULT_SUCCESS = 'CreditUpdated';
    const RESULT_ERROR = 'CreditUpdateFailed';
    
    private $dm;

    /**
     * Creates a new LanguageForm
     */
    function __construct($action)
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
        $this->addElement('html', '<span class="category">' . Translation :: get('Required', null, Utilities :: COMMON_LIBRARIES) . '</span>');
        
        // Name
        $this->addElement('text', 'credits', Translation :: get('Credits'));
        $this->addRule('credits', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        
        // Submit button
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));
        
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
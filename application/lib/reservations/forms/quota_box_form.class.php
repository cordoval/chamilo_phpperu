<?php
/**
 * $Id: quota_box_form.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.forms
 */
require_once dirname(__FILE__) . '/../quota_box.class.php';
require_once dirname(__FILE__) . '/../reservations_data_manager.class.php';

class QuotaBoxForm extends FormValidator
{
    
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'QuotaBoxUpdated';
    const RESULT_ERROR = 'QuotaBoxUpdateFailed';
    
    private $quota_box;
    private $user;
    private $form_type;

    /**
     * Creates a new LanguageForm
     */
    function QuotaBoxForm($form_type, $action, $quota_box, $user)
    {
        parent :: __construct('quota_box_form', 'post', $action);
        
        $this->quota_box = $quota_box;
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
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Required') . '</span>');
        
        $this->addElement('text', QuotaBox :: PROPERTY_NAME, Translation :: get('Title'));
        $this->addRule(QuotaBox :: PROPERTY_NAME, Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor(QuotaBox :: PROPERTY_DESCRIPTION, Translation :: get('Description'), false);
        
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        $this->addElement('html', '<div class="configuration_form">');
        $this->addElement('html', '<span class="category">' . Translation :: get('Quota') . '</span>');
        
        $quotas = ReservationsDataManager :: get_instance()->retrieve_quotas();
        while ($q = $quotas->next_result())
            $quota[$q->get_id()] = $q->get_credits() . ' ' . Translation :: get('Credits') . ' - ' . $q->get_time_unit() . ' ' . Translation :: get('Day(s)') . '';
        
        $this->addElement('advmultiselect', 'quota', Translation :: get('SelectQuota'), $quota, array('style' => 'width:300px; height: 300px'));
        
        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div>');
        
        // Submit button
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
        $this->addElement('hidden', QuotaBox :: PROPERTY_ID);
    }

    function create_quota_box()
    {
        $quota_box = $this->quota_box;
        $values = $this->exportValues();
        
        $quota_box->set_name($values[QuotaBox :: PROPERTY_NAME]);
        $quota_box->set_description($values[QuotaBox :: PROPERTY_DESCRIPTION]);
        
        $succes = $quota_box->create();
        $this->update_quota_rel_quota_box($values, $quota_box);
        
        if ($succes)
            Events :: trigger_event('create_quota_box', 'reservations', array('target_id' => $quota_box->get_id(), 'user_id' => $this->user->get_id()));
        
        return $succes;
    
    }

    function update_quota_box()
    {
        $quota_box = $this->quota_box;
        $values = $this->exportValues();
        
        $quota_box->set_name($values[QuotaBox :: PROPERTY_NAME]);
        $quota_box->set_description($values[QuotaBox :: PROPERTY_DESCRIPTION]);
        
        $succes = $quota_box->update();
        $this->update_quota_rel_quota_box($values, $quota_box);
        
        if ($succes)
            Events :: trigger_event('update_quota_box', 'reservations', array('target_id' => $quota_box->get_id(), 'user_id' => $this->user->get_id()));
        
        return $succes;
    }

    function update_quota_rel_quota_box($values, $quota_box)
    {
        ReservationsDataManager :: get_instance()->delete_quota_from_quota_box($quota_box->get_id());
        
        $succes = true;
        $selected_quota = $values['quota'];
        foreach ($selected_quota as $quotum)
        {
            $quota_rel_quota_box = new QuotaRelQuotaBox();
            $quota_rel_quota_box->set_quota_box_id($quota_box->get_id());
            $quota_rel_quota_box->set_quota_id($quotum);
            $succes &= $quota_rel_quota_box->create();
        }
        
        return $succes;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $quota_box = $this->quota_box;
        $defaults[QuotaBox :: PROPERTY_ID] = $quota_box->get_id();
        $defaults[QuotaBox :: PROPERTY_NAME] = $quota_box->get_name();
        $defaults[QuotaBox :: PROPERTY_DESCRIPTION] = $quota_box->get_description();
        
        $quotas = ReservationsDataManager :: get_instance()->retrieve_quota_rel_quota_boxes(new EqualityCondition(QuotaRelQuotaBox :: PROPERTY_QUOTA_BOX_ID, $quota_box->get_id()));
        while ($quotum = $quotas->next_result())
        {
            $quota[] = $quotum->get_quota_id();
        }
        
        $defaults['quota'] = $quota;
        parent :: setDefaults($defaults);
    }
}
?>
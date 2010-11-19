<?php

namespace application\reservations;

use common\libraries\FormValidator;
use common\libraries\Translation;
use tracking\Event;
use tracking\ChangesTracker;
use common\libraries\Utilities;
/**
 * $Id: quota_form.class.php 217 2009-11-13 14:12:25Z chellee $
 * @package application.reservations.forms
 */
class QuotaForm extends FormValidator
{

    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;
    const RESULT_SUCCESS = 'QuotaUpdated';
    const RESULT_ERROR = 'QuotaUpdateFailed';

    private $quota;
    private $user;
    private $form_type;

    /**
     * Creates a new LanguageForm
     */
    function __construct($form_type, $action, $quota, $user)
    {
        parent :: __construct('quota_form', 'post', $action);

        $this->quota = $quota;
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
        $this->addElement('html', '<span class="category">' . Translation :: get('Required', null, Utilities :: COMMON_LIBRARIES) . '</span>');

        // Credits
        $this->addElement('text', Quota :: PROPERTY_CREDITS, Translation :: get('Credits'));
        $this->addRule(Quota :: PROPERTY_CREDITS, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $this->addElement('text', Quota :: PROPERTY_TIME_UNIT, Translation :: get('TimeUnitInDays'));
        $this->addRule(Quota :: PROPERTY_TIME_UNIT, Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');

        // Submit button
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Save', null, Utilities :: COMMON_LIBRARIES), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

        $this->addElement('html', '<div style="clear: both;"></div>');
        $this->addElement('html', '</div></div>');
    }

    /**
     * Builds an editing form
     */
    function build_editing_form()
    {
        $this->addElement('hidden', Quota :: PROPERTY_ID);
    }

    function create_quota()
    {
        $quota = $this->quota;
        $quota->set_credits($this->exportValue(Quota :: PROPERTY_CREDITS));
        $quota->set_time_unit($this->exportValue(Quota :: PROPERTY_TIME_UNIT));
        $succes = $quota->create();

        if ($succes)
        {
            Event :: trigger('create_quota', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $quota->get_id(), ChangesTracker :: PROPERTY_USER_ID => $this->user->get_id()));
        }

        return $succes;
    }

    function update_quota()
    {
        $quota = $this->quota;
        $quota->set_credits($this->exportValue(Quota :: PROPERTY_CREDITS));
        $quota->set_time_unit($this->exportValue(Quota :: PROPERTY_TIME_UNIT));
        $succes = $quota->update();

        if ($succes)
        {
            Event :: trigger('update_quota', ReservationsManager :: APPLICATION_NAME, array(ChangesTracker :: PROPERTY_REFERENCE_ID => $quota->get_id(), ChangesTracker :: PROPERTY_USER_ID => $this->user->get_id()));
        }

        return $succes;
    }

    /**
     * Sets default values.
     * @param array $defaults Default values for this form's parameters.
     */
    function setDefaults($defaults = array ())
    {
        $quota = $this->quota;
        $defaults[Quota :: PROPERTY_ID] = $quota->get_id();
        $defaults[Quota :: PROPERTY_CREDITS] = $quota->get_credits();
        $defaults[Quota :: PROPERTY_TIME_UNIT] = $quota->get_time_unit();
        parent :: setDefaults($defaults);
    }
}
?>
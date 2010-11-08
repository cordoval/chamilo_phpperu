<?php
namespace application\context_linker;
use common\libraries\FormValidator;
use common\libraries\Translation;

class ConfirmationForm extends FormValidator
{
    function confirmationForm($action)
    {
        parent :: __construct('confirmation', 'post', $action);
    }

    function build_confirmation_form()
    {
        $buttons[] = $this->createElement('style_submit_button', 'yes', Translation :: get('ConfirmYes', null, Utilities :: COMMON_LIBRARY), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_submit_button', 'no', Translation :: get('ConfirmNo', null, Utilities :: COMMON_LIBRARY), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>

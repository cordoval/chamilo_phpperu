<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class ConfirmationForm extends FormValidator
{
    function confirmationForm($action)
    {
        parent :: __construct('confirmation', 'post', $action);
    }

    function build_confirmation_form()
    {
        $buttons[] = $this->createElement('style_submit_button', 'yes', Translation :: get('Yes'), array('class' => 'positive'));
        $buttons[] = $this->createElement('style_submit_button', 'no', Translation :: get('No'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>

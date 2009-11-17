<?php
/**
 * $Id: action_selection_subscribe_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package groups.lib.group_manager.component.wizards.subscribe
 */
require_once dirname(__FILE__) . '/subscribe_wizard_page.class.php';
/**
 * This form can be used to let the user select the action.
 */
class ActionSelectionSubscribeWizardPage extends SubscribeWizardPage
{
    /**
     * Constant defining the action to remove all publications from a course
     */
    const ACTION_EMPTY = 1;
    /**
     * Constant defining the action to copy publications form a course to one or
     * more other courses
     */
    const ACTION_COPY = 2;
    /**
     * Constant defining the action to create a backup of a course
     */
    const ACTION_BACKUP = 3;
    /**
     * Constant defining the action to completely remove a course
     */
    const ACTION_DELETE = 4;
    
    const ACTION_SUBSCRIBE = 5;

    function buildForm()
    {
        $this->addElement('radio', 'action', Translation :: get('SubscribeUsers'), Translation :: get('Subscribe'), self :: ACTION_SUBSCRIBE);
        $this->addRule('action', Translation :: get('ThisFieldIsRequired'), 'required');
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }
}
?>
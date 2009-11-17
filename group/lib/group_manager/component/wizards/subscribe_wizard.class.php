<?php
/**
 * $Id: subscribe_wizard.class.php 130 2009-11-09 13:14:04Z vanpouckesven $
 * Course maintenance tool
 * @package application.weblcms.tool
 * @subpackage maintenance
 */
require_once 'HTML/QuickForm/Controller.php';
require_once 'HTML/QuickForm/Rule.php';
require_once 'HTML/QuickForm/Action/Display.php';
require_once dirname(__FILE__) . '/subscribe/group_selection_subscribe_wizard_page.class.php';
require_once dirname(__FILE__) . '/subscribe/user_selection_subscribe_wizard_page.class.php';
require_once dirname(__FILE__) . '/subscribe/action_selection_subscribe_wizard_page.class.php';
require_once dirname(__FILE__) . '/subscribe/confirmation_subscribe_wizard_page.class.php';
require_once dirname(__FILE__) . '/subscribe/subscribe_wizard_process.class.php';
require_once dirname(__FILE__) . '/subscribe/subscribe_wizard_display.class.php';
/**
 * A wizard which guides the user to several steps to complete a maintenance
 * action on a course.
 */
class SubscribeWizard extends HTML_QuickForm_Controller
{
    /**
     * The repository tool in which this wizard runs.
     */
    private $parent;

    /**
     * Creates a new MaintenanceWizard
     * @param Tool $parent The repository tool in which this wizard
     * runs.
     */
    function SubscribeWizard($parent)
    {
        $this->parent = $parent;
        parent :: HTML_QuickForm_Controller('SubscribeWizard', true);
        $this->addPage(new ActionSelectionSubscribeWizardPage('action_selection', $this->parent));
        $this->addAction('process', new SubscribeWizardProcess($this->parent));
        $this->addAction('display', new SubscribeWizardDisplay($this->parent));
        $values = $this->exportValues();
        $action = null;
        $action = isset($values['action']) ? $values['action'] : null;
        $action = is_null($action) ? $_POST['action'] : $action;
        switch ($action)
        {
            case ActionSelectionSubscribeWizardPage :: ACTION_SUBSCRIBE :
                $this->addPage(new GroupSelectionSubscribeWizardPage('group_selection', $this->parent, $this->parent->get_group()));
                $this->addPage(new UserSelectionSubscribeWizardPage('user_selection', $this->parent));
                $this->addPage(new ConfirmationSubscribeWizardPage('confirmation', $this->parent, Translation :: get('SubscribeConfirmationQuestion')));
                break;
        }
    }
}
?>
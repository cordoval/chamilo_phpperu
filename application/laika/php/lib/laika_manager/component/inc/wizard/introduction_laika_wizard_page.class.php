<?php
/**
 * $Id: introduction_laika_wizard_page.class.php 196 2009-11-13 12:19:18Z chellee $
 * @package application.lib.laika.laika_manager.component.inc.wizard
 */
require_once WebApplication :: get_application_class_lib_path('laika') . 'laika_manager/component/inc/laika_wizard_page.class.php';
/**
 * This form can be used to let the user confirm the selected action.
 */
class IntroductionLaikaWizardPage extends LaikaWizardPage
{
    /**
     * The message which has to be confirmed by the user
     */
    private $message;

    /**
     * Constructor
     * @param string $name The name of this MaintenanceWizardPage
     * @param Tool $parent The repository tool in which this
     * MaintenanceWizardPage is used
     * @param string $message The message which has to be confirmed by the user
     */
    public function IntroductionLaikaWizardPage($name, $parent, $message)
    {
        parent :: LaikaWizardPage($name, $parent);
        $this->message = $message;
    }

    /**
     * Builds the form.
     * The message is showed to the user and a checkbox is added to allow the
     * user to confirm the message.
     */
    function buildForm()
    {
        $this->addElement('static', '', '', $this->message);
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }
}
?>
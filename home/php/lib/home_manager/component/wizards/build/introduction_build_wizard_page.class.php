<?php
namespace home;
use common\libraries\Translation;
/**
 * $Id: introduction_build_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package home.lib.home_manager.component.wizards.build
 */
require_once dirname(__FILE__) . '/build_wizard_page.class.php';
/**
 * This form can be used to let the user confirm the selected action.
 */
class IntroductionBuildWizardPage extends BuildWizardPage
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
    public function __construct($name, $parent, $message)
    {
        parent :: __construct($name, $parent);
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
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next', null, Utilities::COMMON_LIBRARIES) . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }
}
?>
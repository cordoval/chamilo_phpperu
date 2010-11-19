<?php
namespace application\weblcms\tool\maintenance;

use common\libraries\Translation;
use common\libraries\Utilities;

/**
 * $Id: confirmation_maintenance_wizard_page.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.maintenance.inc.wizard
 */
require_once dirname(__FILE__) . '/maintenance_wizard_page.class.php';
/**
 * This form can be used to let the user confirm the selected action.
 */
class ConfirmationMaintenanceWizardPage extends MaintenanceWizardPage
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
        parent :: MaintenanceWizardPage($name, $parent);
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
        $this->addElement('checkbox', 'confirm', ' ', Translation :: get('Confirm', null, Utilities :: COMMON_LIBRARIES));
        $this->addRule('confirm', Translation :: get('ThisFieldIsRequired', null, Utilities :: COMMON_LIBRARIES), 'required');
        $prevnext[] = $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous', null, Utilities :: COMMON_LIBRARIES));
        $prevnext[] = $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next', null, Utilities :: COMMON_LIBRARIES) . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }
}
?>
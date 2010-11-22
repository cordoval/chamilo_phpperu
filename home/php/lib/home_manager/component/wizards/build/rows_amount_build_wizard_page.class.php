<?php
namespace home;
use common\libraries\Translation;
/**
 * $Id: rows_amount_build_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package home.lib.home_manager.component.wizards.build
 */
require_once dirname(__FILE__) . '/build_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class RowsAmountBuildWizardPage extends BuildWizardPage
{

    public function __construct($name, $parent)
    {
        parent :: __construct($name, $parent);
    }

    function buildForm()
    {
        $this->addElement('static', '', '', Translation :: get('BuildRowsAmountMessage'));
        $this->addElement('text', 'rowsamount', Translation :: get('BuildRowsAmount'), array("size" => "50"));
        $this->addRule('rowsamount', Translation :: get('FieldMustBeNumeric', null, Utilities::COMMON_LIBRARIES), 'numeric');
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous', null, Utilities::COMMON_LIBRARIES));
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next', null, Utilities::COMMON_LIBRARIES) . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }
}
?>
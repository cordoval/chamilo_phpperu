<?php

namespace application\package;

use common\libraries\WebApplication;
use HTML_QuickForm_Controller;
/**
 * $Id: exporter_wizard.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 */

require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translation_exporter/pages/exporter_wizard_page.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translation_exporter/pages/branch_selecting_exporter_wizard_page.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translation_exporter/pages/language_selecting_exporter_wizard_page.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translation_exporter/pages/language_pack_selecting_exporter_wizard_page.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translation_exporter/pages/exporter_wizard_process.class.php';
require_once WebApplication :: get_application_class_lib_path('package') . 'package_manager/component/translation_exporter/pages/exporter_wizard_display.class.php';


/**
 * A wizard which exports translations
 */
class ExporterWizard extends HTML_QuickForm_Controller
{
    /**
     * The parent
     */
    private $parent;

    /**
     * Creates a new ExporterWizard
     * @param $parent The parent
     */
    function __construct($parent)
    {
        $this->parent = $parent;
        parent :: __construct('ExporterWizard', true);
        $this->addPage(new BranchSelectingExporterWizardPage('page_branch_selecting', $this));
        $this->addPage(new LanguageSelectingExporterWizardPage('page_language_selecting', $this));
        $this->addPage(new LanguagePackSelectingExporterWizardPage('page_language_pack_selecting', $this));

        $this->addAction('process', new ExporterWizardProcess($this->parent));
        $this->addAction('display', new ExporterWizardDisplay($this->parent));
    }

    function get_parent()
    {
    	return $this->parent;
    }

    function get_pages()
    {
    	return $this->_pages;
    }
}
?>
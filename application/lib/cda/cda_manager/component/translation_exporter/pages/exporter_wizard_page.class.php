<?php
/**
 * $Id: exporter_wizard_page.class.php 204 2009-11-13 12:51:30Z kariboe $
 */
/**
 * This abstract class defines a page which is used in a exporter wizard.
 */


abstract class ExporterWizardPage extends FormValidatorPage
{
    private $parent;

    public function ExporterWizardPage($name, $parent)
    {
        $this->parent = $parent;
        parent :: __construct($name, 'post');
        $this->updateAttributes(array('action' => $parent->get_parent()->get_export_translations_url()));
    }
    
    function get_parent()
    {
        return $this->parent;
    }

}
?>
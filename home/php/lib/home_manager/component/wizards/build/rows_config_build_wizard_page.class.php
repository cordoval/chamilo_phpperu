<?php
/**
 * $Id: rows_config_build_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package home.lib.home_manager.component.wizards.build
 */
require_once dirname(__FILE__) . '/build_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class RowsConfigBuildWizardPage extends BuildWizardPage
{
    private $values;

    public function RowsConfigBuildWizardPage($name, $parent, $values)
    {
        parent :: BuildWizardPage($name, $parent);
        $this->values = $values;
    }

    function buildForm()
    {
        $values = $this->values;
        $row_amount = $values['rowsamount'];
        
        $this->addElement('static', '', '', $this->get_preview_html());
        $this->addElement('static', '', '', '<br />');
        
        for($i = 1; $i <= $row_amount; $i ++)
        {
            $this->addElement('static', '', '', '<b>' . Translation :: get('Row') . '&nbsp;' . $i . '</b>');
            $this->addElement('text', 'row' . $i . '[title]', Translation :: get('Title'), array("size" => "50"));
            $this->addElement('text', 'row' . $i . '[columnsamount]', Translation :: get('Columns'), array("size" => "50"));
            $this->addRule('row' . $i . '[columnsamount]', Translation :: get('FieldMustBeNumeric'), 'numeric');
            $this->addRule('row' . $i . '[title]', Translation :: get('ThisFieldIsRequired'), 'required');
            $this->addRule('row' . $i . '[columnsamount]', Translation :: get('ThisFieldIsRequired'), 'required');
            
            if ($i != $row_amount)
            {
                $this->addElement('static', '', '', '<br /><br />');
            }
        }
        
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('back'), '<< ' . Translation :: get('Previous'));
        $prevnext[] = & $this->createElement('submit', $this->getButtonName('next'), Translation :: get('Next') . ' >>');
        $this->addGroup($prevnext, 'buttons', '', '&nbsp;', false);
        $this->setDefaultAction('next');
        $this->_formBuilt = true;
    }

    function get_preview_html()
    {
        $values = $this->values;
        $row_amount = $values['rowsamount'];
        
        $html = array();
        
        $html[] = '<b>' . Translation :: get('SchematicPreview') . '</b>';
        $html[] = '<div style="border: 1px solid #000000; padding: 15px;width: 500px;">';
        
        for($i = 1; $i <= $row_amount; $i ++)
        {
            $html[] = '<div class="row" style="' . ($i < $row_amount ? 'margin-bottom: 15px;' : '') . 'padding: 10px; text-align: center; height: 20px; line-height: 20px; font-size: 20pt;' . ($i % 2 == 1 ? 'background-color: #7c8db8; color: #FFFFFF;' : 'background-color: #b87c82; color: #000000;') . '">';
            $html[] = Translation :: get('Row') . '&nbsp;' . $i;
            $html[] = '</div>';
            $html[] = '<div style="clear: both;"></div>';
        }
        
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }
}
?>
<?php
/**
 * $Id: columns_config_build_wizard_page.class.php 141 2009-11-10 07:44:45Z kariboe $
 * @package home.lib.home_manager.component.wizards.build
 */
require_once dirname(__FILE__) . '/build_wizard_page.class.php';
/**
 * This form can be used to let the user select publications in the course.
 */
class ColumnsConfigBuildWizardPage extends BuildWizardPage
{
    private $values;

    public function ColumnsConfigBuildWizardPage($name, $parent, $values)
    {
        parent :: BuildWizardPage($name, $parent);
        $this->values = $values;
    }

    function buildForm()
    {
        $values = $this->values;
        $row_amount = $values['rowsamount'];
        
        $this->addElement('static', null, null, $this->get_preview_html());
        $this->addElement('static', null, null, '<script type="text/javascript" src="' . $this->get_parent()->get_path(WEB_LIB_PATH) . 'javascript/home_admin.js"></script>');
        $this->addElement('static', null, null, '<br />');
        
        for($i = 1; $i <= $row_amount; $i ++)
        {
            $column_amount = $values['row' . $i]['columnsamount'];
            
            $this->addElement('text', 'row' . $i . 'width', Translation :: get('WidthLeft'), array('disabled' => 1, 'id' => 'row' . $i . 'width', "size" => "50"));
            
            for($j = 1; $j <= $column_amount; $j ++)
            {
                // TODO: Also pass on site width to javascript once this can be changed.
                $this->addElement('static', '', '', '<b>' . Translation :: get('Row') . '&nbsp;' . $i . '&nbsp;-&nbsp;' . Translation :: get('Column') . '&nbsp;' . $j . '</b>');
                $this->addElement('text', 'row' . $i . '[column' . $j . '][title]', Translation :: get('Title'), array("size" => "50"));
                $this->addElement('text', 'row' . $i . '[column' . $j . '][width]', Translation :: get('Width'), 'size="50" onchange="recalculate(' . $i . ',' . $column_amount . ')"');
                $this->addElement('text', 'row' . $i . '[column' . $j . '][blocksamount]', Translation :: get('BlockAmount'), array("size" => "50"));
                $this->addRule('row' . $i . '[column' . $j . '][width]', Translation :: get('FieldMustBeNumeric'), 'numeric');
                $this->addRule('row' . $i . '[column' . $j . '][blocksamount]', Translation :: get('FieldMustBeNumeric'), 'numeric');
                $this->addRule('row' . $i . '[column' . $j . '][title]', Translation :: get('ThisFieldIsRequired'), 'required');
                $this->addRule('row' . $i . '[column' . $j . '][width]', Translation :: get('ThisFieldIsRequired'), 'required');
                $this->addRule('row' . $i . '[column' . $j . '][blocksamount]', Translation :: get('ThisFieldIsRequired'), 'required');
                
                if ($j != $column_amount)
                {
                    $this->addElement('static', '', '', '<br />');
                }
            }
            
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
            $html[] = '<div class="row" style="' . ($i < $row_amount ? 'margin-bottom: 15px;' : '') . 'padding: 10px; text-align: center; height: 40px; line-height: 20px; font-size: 20pt;' . ($i % 2 == 1 ? 'background-color: #7c8db8; color: #FFFFFF;' : 'background-color: #b87c82; color: #000000;') . '">';
            
            $column_amount = $values['row' . $i]['columnsamount'];
            
            for($j = 1; $j <= $column_amount; $j ++)
            {
                
                $column_width = floor((480 - ($column_amount - 1) * 10) / $column_amount) - 20;
                $html[] = '<div class="column" style="' . ($j < $column_amount ? 'margin-right: 10px;' : '') . 'padding: 10px; text-align: center; width: ' . $column_width . 'px; font-size: 10pt;' . ($j % 2 == 1 ? 'background-color: #FFFFFF; color: #000000;' : 'background-color: #e8e8e8; color: #000000;') . '">';
                $html[] = Translation :: get('Row') . '&nbsp;' . $i;
                $html[] = Translation :: get('Column') . '&nbsp;' . $j;
                $html[] = '</div>';
            }
            
            $html[] = '</div>';
            $html[] = '<div style="clear: both;"></div>';
        }
        
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';
        
        return implode("\n", $html);
    }
}
?>
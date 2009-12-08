<?php
/**
 * $Id: install_wizard_display.class.php 225 2009-11-13 14:43:20Z vanpouckesven $
 * @package install.lib.installmanager.component.inc.wizard
 */
/**
 * This class provides the needed functionality to show a page in a maintenance
 * wizard.
 */
class InstallWizardDisplay extends HTML_QuickForm_Action_Display
{
    /**
     * The repository tool in which the wizard runs
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs
     */
    public function InstallWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        $renderer = $current_page->defaultRenderer();
        
        $form_template = <<<EOT

<form {attributes}>
{content}
	<div class="clear">&nbsp;</div>
</form>

EOT;
        $renderer->setFormTemplate($form_template);
        
        $current_page->setRequiredNote('<font color="#FF0000">*</font> ' . Translation :: get('ThisFieldIsRequired'));
        //		$element_template = "\n\t<tr>\n\t\t<td valign=\"top\"><!-- BEGIN required --><span style=\"color: #ff0000\">*</span> <!-- END required -->{label}</td>\n\t\t<td valign=\"top\" align=\"left\"><!-- BEGIN error --><span style=\"color: #ff0000;font-size:x-small;margin:2px;\">{error}</span><br /><!-- END error -->\t{element}</td>\n\t</tr>";
        $element_template = array();
        $element_template[] = '<div class="row">';
        $element_template[] = '<div class="label">';
        $element_template[] = '{label}<!-- BEGIN required --><span class="form_required"><img src="../layout/aqua/images/common/action_required.png" alt="*" title ="*"/></span> <!-- END required -->';
        $element_template[] = '</div>';
        $element_template[] = '<div class="formw">';
        $element_template[] = '<div class="element"><!-- BEGIN error --><span class="form_error">{error}</span><br /><!-- END error -->	{element}</div>';
        $element_template[] = '<div class="form_feedback"></div>';
        $element_template[] = '</div>';
        $element_template[] = '<div class="clear">&nbsp;</div>';
        $element_template[] = '</div>';
        $element_template = implode("\n", $element_template);
        
        $renderer->setElementTemplate($element_template);
        //		$header_template = "\n\t<tr>\n\t\t<td valign=\"top\" colspan=\"2\">{header}</td>\n\t</tr>";
        $header_template = array();
        $header_template[] = '<div class="row">';
        $header_template[] = '<div class="form_header">{header}</div>';
        $header_template[] = '</div>';
        $header_template = implode("\n", $header_template);
        
        $renderer->setHeaderTemplate($header_template);
        HTML_QuickForm :: setRequiredNote('<span class="form_required"><img src="../layout/aqua/images/common/action_required.png" alt="*" title ="*"/>&nbsp;<small>' . Translation :: get('ThisFieldIsRequired') . '</small></span>');
        $required_note_template = <<<EOT
	<div class="row">
		<div class="label"></div>
		<div class="formw">{requiredNote}</div>
	</div>
EOT;
        $renderer->setRequiredNoteTemplate($required_note_template);
        
        $current_page->accept($renderer);
        
        $this->parent->display_header(array(), 'install');
        
        echo '<div id="progressbox">';
        $all_pages = $current_page->controller->_pages;
        $total_number_of_pages = count($all_pages);
        $current_page_number = 0;
        $page_number = 0;
        echo '<ul id="progresstrail">';
        foreach ($all_pages as $index => $page)
        {
        	$page_number ++;
            
            if ($page->get_title() == $current_page->get_title())
            {
                $current_page_number = $page_number;
                //				echo '<li class="active"><a href="#">' . $page_number . '.&nbsp;&nbsp;' . $page->get_title() . '</a></li>';
            }
            else
            {
                //				echo '<li><a href="#">' . $page_number . '.&nbsp;&nbsp;' . $page->get_title() . '</a></li>';
            }
        }
        
        $page_number = 0;
        foreach ($all_pages as $index => $page)
        {
        	if(get_class($current_page) == 'LanguageInstallWizardPage' && get_class($page) != 'LanguageInstallWizardPage')
            {
            	continue;
            }
            
        	if(get_class($current_page) == 'PreconfiguredInstallWizardPage')
            {
            	if(get_class($page) != 'PreconfiguredInstallWizardPage' && get_class($page) != 'LanguageInstallWizardPage')
            		continue;
            }
            
        	if(get_class($page) == 'PreconfiguredInstallWizardPage')
            {
            	if(get_class($current_page) != 'PreconfiguredInstallWizardPage')
            		continue;
            }
            
        	$page_number ++;
            
            if ($page_number <= $current_page_number)
            {
                echo '<li class="active"><a href="#">' . $page_number . '.&nbsp;&nbsp;' . $page->get_title() . '</a></li>';
            }
            else
            {
                echo '<li><a href="#">' . $page_number . '.&nbsp;&nbsp;' . $page->get_title() . '</a></li>';
            }
        }
        
        echo '</ul>';
        echo '<div class="clear"></div>';
        echo '</div>' . "\n";
        
        echo '<div id="theForm" style="margin: 10px;">';
        echo '<div id="select" class="row"><div class="formc formc_no_margin">';
        echo '<b>' . Translation :: get('Step') . ' ' . $current_page_number . ' ' . Translation :: get('of') . ' ' . $total_number_of_pages . ' &ndash; ' . $current_page->get_title() . '</b><br />';
        
        //		echo '<h2>'.Translation :: get('Step').' '.$current_page_number.' '.Translation :: get('of').' '.$total_number_of_pages.' &ndash; '.$current_page->get_title().'</h2>';
        echo $current_page->get_info();
        echo '</div>';
        echo '</div>';
        
        if (isset($_SESSION['install_message']))
        {
            Display :: normal_message($_SESSION['install_message']);
            unset($_SESSION['install_message']);
        }
        if (isset($_SESSION['install_error_message']))
        {
            Display :: error_message($_SESSION['install_error_message']);
            unset($_SESSION['install_error_message']);
        }
        
        parent :: _renderForm($current_page);
        //		echo '</div>';
        echo '</div>';
        
        $this->parent->display_footer();
    }
}
?>
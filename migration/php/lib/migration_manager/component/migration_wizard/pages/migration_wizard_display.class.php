<?php
/**
 * $Id: migration_wizard_display.class.php 221 2009-11-13 14:36:41Z vanpouckesven $
 * @package migration.lib.migration_manager.component.inc.wizard
 */


/**
 * This class provides the needed functionality to show a page in a migration
 * wizard.
 *
 * @author Sven Vanpoucke
 */
class MigrationWizardDisplay extends HTML_QuickForm_Action_Display
{
    /**
     * The Migration Wizard Component in which the wizard runs
     */
    private $parent;

    /**
     * Constructor
     * @param Tool $parent The repository tool in which the wizard
     * runs
     */
    public function MigrationWizardDisplay($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
    	$this->display_header();
        
        $this->display_pages_trail($current_page);
        $this->display_page_info($current_page);
        $this->display_progress_bar();
        $this->display_page_html($current_page);
        $this->display_form($current_page);
        $this->display_next_page_info($current_page);

        $this->display_footer();
    }
    
    function get_parent()
    {
    	return $this->parent;
    }
    
    function display_header()
    {
    	return $this->get_parent()->display_header();
    }
    
    function display_footer()
    {
    	return $this->get_parent()->display_footer();
    }
    
    function display_pages_trail($current_page)
    {
    	$counter = 1;
        
        echo '<ul id="progresstrail">';
        echo '<li class="active"><a href="#">' . $counter . '. ' . Translation :: get('Confirm') . '</a></li>';
        
        $counter++;
        
        $is_confirm_page = ($current_page->get_name() == 'confirmation_page');
        
        $before_current_page = true;
        $blocks = $this->parent->get_blocks();
        
        foreach($blocks as $block)
        {
        	if(!$is_confirm_page && $before_current_page)
        	{
        		echo '<li class="active">';
        	}
        	else
        	{
        		echo '<li>';
        	}
        	
        	echo '<a href="#">' . $counter . '. ' . Translation :: get('Migrate' . Utilities :: underscores_to_camelcase($block)) . '</a></li>';
	        $counter++;
        	
        	if(!$is_confirm_page && $current_page->get_block() == $block)
        	{
        		$before_current_page = false;
        	}
        }
       
        echo '</ul>';
        echo '<div class="clear"></div>';
    }
    
    function display_page_info($current_page)
    {
    	echo'<div class="migration_info">';
        $current_page->display_page_info();
        echo '</div>';
    }
    
    function display_progress_bar()
    {
    	echo '<div id="dynamic_div" style="display:block;margin-left:40%;margin-top:10px;height:50px;"></div>';
        echo '<script src="' . Path :: get(WEB_LIB_PATH) . 'javascript/upload.js" type="text/javascript"></script>';
        echo '<script type="text/javascript">var myUpload = new upload(' . (abs(intval(1)) * 1000) . '); myUpload.start(\'dynamic_div\',\'' . Theme :: get_common_image_path() . 'action_progress_bar.gif\',\'' . Translation :: get('PleaseStandBy') . '\',\'\');</script>';
        flush();
        echo '<script type="text/javascript">myUpload.stop();</script>';
		
    }
    
    function display_form($current_page)
    {
    	echo '<br />';
        parent :: _renderForm($current_page);
		echo '<br />';
    }
    
    function display_next_page_info($current_page)
    {
    	echo'<div class="migration_info">';
        $current_page->display_next_page_info();
        echo '</div>';
    }
    
	function display_page_html($current_page)
	{ 
		echo '<div class="page_html">';
        $current_page->display_page_html();
        echo '</div>';
	}
}
?>
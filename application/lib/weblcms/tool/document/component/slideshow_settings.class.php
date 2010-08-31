<?php
/**
 * $Id: document_viewer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.document.component
 */
/*require_once dirname(__FILE__) . '/../document_tool.class.php';
require_once dirname(__FILE__) . '/../document_tool_component.class.php';*/
require_once dirname(__FILE__) . '/document_slideshow/document_slideshow_settings_form.class.php';

class DocumentToolSlideshowSettingsComponent extends DocumentTool
{
    function run()
    {
        if (! $this->is_allowed(WeblcmsRights :: VIEW_RIGHT))
        {
            Display :: not_allowed();
            return;
        }
         
        $form = new DocumentSlideshowSettingsForm($this->get_url(), $this->get_user_id());
        if($form->validate())
        {
        	$form->update_settings();
        	$this->redirect(Translation :: get('SettingsUpdated'), false, array(Tool :: PARAM_ACTION => DocumentTool :: ACTION_SLIDESHOW));
        }
        else
        {
	        $trail = BreadcrumbTrail :: get_instance();
	        $trail->add(new Breadcrumb($this->get_url(array(Tool :: PARAM_ACTION => DocumentTool :: ACTION_SLIDESHOW)), Translation :: get('Slideshow')));
	        $trail->add(new Breadcrumb($this->get_url(array()), Translation :: get('SlideshowSettings')));
	        
	        $this->display_header();
	        $form->display();
	        $this->display_footer();
        }
        
    }
}
?>
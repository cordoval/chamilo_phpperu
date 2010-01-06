<?php
/**
 * $Id: diagnoser.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

/**
 * Weblcms component displays diagnostics about the system
 */
class AdminManagerImporterComponent extends AdminManagerComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER)), Translation :: get('Administration')));
        $trail->add(new Breadcrumb(Redirect :: get_link(AdminManager :: APPLICATION_NAME, array(AdminManager :: PARAM_ACTION => AdminManager :: ACTION_ADMIN_BROWSER, 'selected' => AdminManager :: APPLICATION_NAME), array(), false, Redirect :: TYPE_CORE), Translation :: get('Admin')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Importer')));
        $trail->add_help('administration importer');
        
        if (! AdminRights :: is_allowed(AdminRights :: VIEW_RIGHT, 'root', 'root'))
        {
            $this->display_header($trail);
            $this->display_error_message(Translation :: get('NotAllowed'));
            $this->display_footer();
            exit();
        }
        
        $this->display_header($trail);
        
        echo $this->display_import_buttons();
        
        $this->display_footer();
    }
    
    function display_import_buttons()
    {
    	$count = 0;
    	
    	$html[] = '<div class="tab">';
    	$html[] = '<div class="items">';
    	
    	$import_links = $this->get_application_platform_import_links();
    	
    	foreach($import_links as $link)
    	{
	    	$html[] = '<div class="vertical_action"' . ($count == 0 ? ' style="border-top: 0px solid #FAFCFC;"' : '') . '>';
	        $html[] = '<div class="icon">';
	        $html[] = '<a href="' . $link['url'] . '"><img src="' . Theme :: get_image_path() . 'browse_import.png" alt="' . $link['name'] . '" title="' . $link['name'] . '"/></a>';
	        $html[] = '</div>';
	        $html[] = '<div class="description">';
	        $html[] = '<h4><a href="' . $link['url'] . '">' . $link['name'] . '</a></h4>';
	        $html[] = $link['description'];
	        $html[] = '</div>';
	        $html[] = '</div>';
	        
	        $count++;
    	}
        
    	$html[] = '</div>';
    	$html[] ='</div>';
    	
    	return implode("\n", $html);
    }

}
?>
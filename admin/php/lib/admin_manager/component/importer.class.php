<?php
namespace admin;
use common\libraries\Utilities;
use common\libraries\Translation;
use common\libraries\AdministrationComponent;
use common\libraries\Theme;
/**
 * $Id: diagnoser.class.php 168 2009-11-12 11:53:23Z vanpouckesven $
 * @package admin.lib.admin_manager.component
 */

/**
 * Weblcms component displays diagnostics about the system
 */
class AdminManagerImporterComponent extends AdminManager implements AdministrationComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
//        if (! AdminRights :: is_allowed(AdminRights :: RIGHT_VIEW))
//        {
//            $this->display_header();
//            $this->display_error_message(Translation :: get('NotAllowed', array(), Utilities :: COMMON_LIBRARIES));
//            $this->display_footer();
//            exit();
//        }

        $this->display_header();

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

	function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('admin_importer');
    }

}
?>
<?php
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
/**
 */
class ComplexBuilderViewerComponent extends ComplexBuilderComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(ComplexBuilder :: PARAM_SELECTED_CLOI_ID);
        
        if($id)
        {
        	$cloi = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($id);
        	$lo = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_ref());
        	
        	$trail = new BreadcrumbTrail(false);
        	$menu_trail = $this->get_clo_breadcrumbs();
        	$trail->merge($menu_trail);
        	$parameters = array(ComplexBuilder :: PARAM_ROOT_LO => Request :: get(ComplexBuilder :: PARAM_ROOT_LO), 
        					    ComplexBuilder :: PARAM_CLOI_ID => Request :: get(ComplexBuilder :: PARAM_CLOI_ID),
        					    ComplexBuilder :: PARAM_SELECTED_CLOI_ID => $id, 
        						'publish' => Request :: get('publish'));

        	$trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('View') . ' ' . $lo->get_title()));
        	
        	$this->display_header($trail);
        	
        	$display = ContentObjectDisplay :: factory($lo);
        	echo $display->get_full_html();
        	
        	$this->display_footer();
        }
        else
        {
            $this->display_error_page(htmlentities(Translation :: get('NoObjectSelected')));
        }
    }
}
?>
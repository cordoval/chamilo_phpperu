<?php
/**
 * $Id: deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.component
 */
require_once dirname(__FILE__) . '/../complex_builder_component.class.php';
/**
 */
class ComplexBuilderComponentViewerComponent extends ComplexBuilderComponent
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $id = Request :: get(ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);

        if ($id)
        {
            $complex_content_object_item = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_item($id);
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object_item->get_ref());

            $trail = BreadcrumbTrail :: get_instance();
            $this->get_complex_content_object_breadcrumbs();
            $parameters = array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->get_parent()->get_complex_content_object_item_id(), ComplexBuilder :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $id);
            $trail->add(new Breadcrumb($this->get_url($parameters), Translation :: get('View') . ' ' . $content_object->get_title()));

            $this->display_header($trail);

            $display = ContentObjectDisplay :: factory($content_object);
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
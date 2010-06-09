<?php
/**
 * $Id: learning_path_browser_table_cell_renderer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.learning_path.component.browser
 */
require_once Path :: get_repository_path() . 'lib/repository_manager/component/complex_browser/complex_browser_table_cell_renderer.class.php';
/**
 * Cell rendere for the learning object browser table
 */
class SurveyBrowserTableCellRenderer extends ComplexBrowserTableCellRenderer
{

    /**
     * Constructor
     * @param RepositoryManagerBrowserComponent $browser
     */
    function SurveyBrowserTableCellRenderer($browser, $condition)
    {
        parent :: __construct($browser, $condition);
    }

    private $lpi_ref_object;

    // Inherited
    function render_cell($column, $cloi)
    {
        $lo = $this->retrieve_content_object($cloi->get_ref());

        if ($column === ComplexBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($cloi, $lo);
        }

        switch ($column->get_name())
        {
            case Translation :: get(Utilities :: underscores_to_camelcase(ContentObject :: PROPERTY_TITLE)) :
                $title = htmlspecialchars($lo->get_title());
                $title_short = $title;
                $title_short = Utilities :: truncate_string($title_short, 53, false);

                if ($lo->is_complex_content_object())
                {
                    $title_short = '<a href="' . $this->browser->get_url(array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $cloi->get_id())) . '">' . $title_short . '</a>';
                }
        		else
                {
                	$title_short = '<a href="' . $this->browser->get_complex_content_object_item_view_url($cloi->get_id()) . '">' . $title_short . '</a>';
                }

                return $title_short;
        }

        return parent :: render_cell($column, $cloi, $lo);
    }

    protected function get_modification_links($cloi, $lo)
    {
        $additional_items = array();
        $parent = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_parent());

        $toolbar_data = array();

        $edit_url = $this->browser->get_complex_content_object_item_edit_url($cloi->get_id());
        if ($cloi->is_extended() || get_parent_class($this->browser) == 'ComplexBuilder')
        {
            $toolbar_data[] = array('href' => $edit_url, 'label' => Translation :: get('Edit'), 'img' => Theme :: get_common_image_path() . 'action_edit.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('EditNA'), 'img' => Theme :: get_common_image_path() . 'action_edit_na.png');
        }
        $configure_url = $this->browser->get_configure_url ( $cloi);
        $delete_url = $this->browser->get_complex_content_object_item_delete_url($cloi->get_id());
        $moveup_url = $this->browser->get_complex_content_object_item_move_url($cloi->get_id(), RepositoryManager :: PARAM_DIRECTION_UP);
        $movedown_url = $this->browser->get_complex_content_object_item_move_url($cloi->get_id(), RepositoryManager :: PARAM_DIRECTION_DOWN);

        if ($lo->get_type() == SurveyPage :: get_type_name())
        {
        	$toolbar_data [] = array ('href' => $configure_url, 'label' => Translation::get ( 'Configure' ), 'img' => Theme::get_common_image_path () . 'action_build_prerequisites.png' );
        }

        $toolbar_data[] = array('href' => $delete_url, 'label' => Translation :: get('Delete'), 'img' => Theme :: get_common_image_path() . 'action_delete.png', 'confirm' => true);

        $allowed = $this->check_move_allowed($cloi);

        if ($allowed["moveup"])
        {
            $toolbar_data[] = array('href' => $moveup_url, 'label' => Translation :: get('MoveUp'), 'img' => Theme :: get_common_image_path() . 'action_up.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveUpNA'), 'img' => Theme :: get_common_image_path() . 'action_up_na.png');

        }

        if ($allowed["movedown"])
        {
            $toolbar_data[] = array('href' => $movedown_url, 'label' => Translation :: get('MoveDown'), 'img' => Theme :: get_common_image_path() . 'action_down.png');
        }
        else
        {
            $toolbar_data[] = array('label' => Translation :: get('MoveDownNA'), 'img' => Theme :: get_common_image_path() . 'action_down_na.png');
        }

        $toolbar_data = array_merge($toolbar_data, $additional_items);

        return Utilities :: build_toolbar($toolbar_data);

    }
}
?>
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

                if ($lo instanceof ComplexContentObjectSupport)
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
        $toolbar = new Toolbar();
        $parent = RepositoryDataManager :: get_instance()->retrieve_content_object($cloi->get_parent());

        if ($cloi->is_extended() || get_parent_class($this->browser) == 'ComplexBuilder')
        {
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Edit'),
        			Theme :: get_common_image_path().'action_edit.png',
					$this->browser->get_complex_content_object_item_edit_url($cloi->get_id()),
				 	ToolbarItem :: DISPLAY_ICON
			));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('EditNA'),
        			Theme :: get_common_image_path().'action_edit_na.png',
					null,
				 	ToolbarItem :: DISPLAY_ICON
			));
        }

        if ($lo->get_type() == SurveyPage :: get_type_name())
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('Configure'),
        			Theme :: get_common_image_path().'action_build_prerequisites.png',
					$this->browser->get_configure_url ( $cloi),
				 	ToolbarItem :: DISPLAY_ICON
			));
        }

        $toolbar->add_item(new ToolbarItem(
        			Translation :: get('Delete'),
        			Theme :: get_common_image_path().'action_delete.png',
					$this->browser->get_complex_content_object_item_delete_url($cloi->get_id()),
				 	ToolbarItem :: DISPLAY_ICON,
				 	true
		));

        $allowed = $this->check_move_allowed($cloi);

        if ($allowed["moveup"])
        {
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('MoveUp'),
        			Theme :: get_common_image_path().'action_up.png',
					$this->browser->get_complex_content_object_item_move_url($cloi->get_id(), RepositoryManager :: PARAM_DIRECTION_UP),
				 	ToolbarItem :: DISPLAY_ICON
			));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('MoveUpNA'),
        			Theme :: get_common_image_path().'action_up_na.png',
					null,
				 	ToolbarItem :: DISPLAY_ICON
			));
        }

        if ($allowed["movedown"])
        {
            $toolbar->add_item(new ToolbarItem(
        			Translation :: get('MoveDown'),
        			Theme :: get_common_image_path().'action_down.png',
					$this->browser->get_complex_content_object_item_move_url($cloi->get_id(), RepositoryManager :: PARAM_DIRECTION_DOWN),
				 	ToolbarItem :: DISPLAY_ICON
			));
        }
        else
        {
        	$toolbar->add_item(new ToolbarItem(
        			Translation :: get('MoveDownNA'),
        			Theme :: get_common_image_path().'action_down_na.png',
					null,
				 	ToolbarItem :: DISPLAY_ICON
			));
        }

        return $toolbar->as_html();
    }
}
?>
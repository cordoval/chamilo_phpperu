<?php
require_once dirname(__FILE__).'/../../../tables/gradebook_publication_table/default_gradebook_publication_table_cell_renderer.class.php';
require_once Path :: get_common_path() . '/datetime/datetime_utilities.class.php';

class GradebookInternalPublicationBrowserTableCellRenderer extends DefaultGradebookPublicationTableCellRenderer
{/**
	 * The browser component
	 */
	private $browser;

	/**
	 * Constructor
	 * @param ApplicationComponent $browser
	 */
	function GradebookInternalPublicationBrowserTableCellRenderer($browser)
	{
		parent :: __construct();
		$this->browser = $browser;
	}

	// Inherited
	function render_cell($column, $internal_item)
	{
		if ($column === GradebookInternalPublicationBrowserTableColumnModel :: get_modification_column())
		{
			return $this->get_modification_links($internal_item);
		}
		$application_manager = WebApplication :: factory($internal_item->get_application());
		$attributes = $application_manager->get_content_object_publication_attribute($internal_item->get_publication_id());
		if(!$attributes)
		{
			return null;
		}
		$rdm = RepositoryDataManager :: get_instance();
		$content_object = $rdm->retrieve_content_object($attributes->get_publication_object_id());
		// Add special features here
        switch ($column->get_name())
        {
			case ContentObject :: PROPERTY_CREATION_DATE :
				return DatetimeUtilities :: format_locale_date(Translation :: get('dateFormatShort') . ', ' . Translation :: get('timeNoSecFormat'), $content_object->get_creation_date());
				break;
        }

		return parent :: render_cell($column, $content_object);
	}

	/**
	 * Gets the action links to display
	 * @param Format $evaluation_format The evaluation format for which the
	 * action links should be returned
	 * @return string A HTML representation of the action links
	 */
	private function get_modification_links($content_object)
	{
		$toolbar= new Toolbar();
        $toolbar->add_item(new ToolbarItem(Translation :: get('Browser'), Theme :: get_common_image_path() . 'action_browser.png', $this->browser->get_internal_evaluations_on_publications_viewer_url($content_object), ToolbarItem :: DISPLAY_ICON ));
        
		return $toolbar->as_html();
	}
	
}
?>
<?php
/**
 * $Id: survey_publication_browser_table_cell_renderer.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.survey.survey_manager.component.survey_publication_browser
 */
require_once dirname(__FILE__) . '/survey_publication_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/../../../tables/survey_publication_table/default_survey_publication_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/../../../survey_publication.class.php';
require_once dirname(__FILE__) . '/../../survey_manager.class.php';

/**
 * Cell rendere for the learning object browser table
 *
 * @author Sven Vanpoucke
 * @author
 */

class SurveyPublicationBrowserTableCellRenderer extends DefaultSurveyPublicationTableCellRenderer
{
    /**
     * The browser component
     * @var SurveyManagerSurveyPublicationsBrowserComponent
     */
    private $browser;

    /**
     * Constructor
     * @param ApplicationComponent $browser
     */
    function SurveyPublicationBrowserTableCellRenderer($browser)
    {
        parent :: __construct();
        $this->browser = $browser;
    }

    // Inherited
    function render_cell($column, $survey_publication)
    {



    	if ($column === SurveyPublicationBrowserTableColumnModel :: get_modification_column())
        {
            return $this->get_modification_links($survey_publication);
        }

       switch ($column->get_name())
        {
            case ContentObject :: PROPERTY_TITLE :
             	$content_object = $survey_publication->get_publication_object();
            	$user = $this->browser->get_user();
            	$title = $content_object->get_title();
        		if ($survey_publication->is_visible_for_target_user($user, true))
        		{
            		$url = '<a href="' . htmlentities($this->browser->get_survey_publication_viewer_url($survey_publication)) . '" title="' . $title . '">' . $title . '</a>';
        		}else{
        			$url = $title;
        		}


            	if ($survey_publication->get_hidden())
                {
                    return '<span style="color: #999999;">' . $url . '</span>';
                }

                return $url;
        }


        return parent :: render_cell($column, $survey_publication);

    }
    /**
     * Gets the action links to display
     * @param SurveyPublication $survey_publication The learning object for which the
     * action links should be returned
     * @return string A HTML representation of the action links
     */
    private function get_modification_links($survey_publication)
    {
        $survey = $survey_publication->get_publication_object();
        $user = $this->browser->get_user();

        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        if ($survey_publication->is_visible_for_target_user($user, true))
        {
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('TakeSurvey'),
	        		Theme :: get_common_image_path() . 'action_next.png',
	        		$this->browser->get_survey_publication_viewer_url($survey_publication),
	        		ToolbarItem :: DISPLAY_ICON
	        ));
        }
        else
        {
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('SurveyPublished'),
	        		Theme :: get_common_image_path() . 'action_next_na.png',
	        		null,
	        		ToolbarItem :: DISPLAY_ICON
	        ));

        }

        if ($user->is_platform_admin() || $user->get_id() == $survey_publication->get_publisher())
        {
            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Edit'),
	        		Theme :: get_common_image_path() . 'action_edit.png',
	        		$this->browser->get_update_survey_publication_url($survey_publication),
	        		ToolbarItem :: DISPLAY_ICON
	        ));

	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Delete'),
	        		Theme :: get_common_image_path() . 'action_delete.png',
	        		$this->browser->get_delete_survey_publication_url($survey_publication),
	        		ToolbarItem :: DISPLAY_ICON,
	        		true
	        ));

            if ($survey_publication->get_hidden())
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Show'),
		        		Theme :: get_common_image_path() . 'action_visible_na.png',
		        		$this->browser->get_change_survey_publication_visibility_url($survey_publication),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
            else
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('Hide'),
		        		Theme :: get_common_image_path() . 'action_visible.png',
		        		$this->browser->get_change_survey_publication_visibility_url($survey_publication),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }

            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('ViewReport'),
	        		Theme :: get_common_image_path() . 'action_view_results.png',
	        		$this->browser->get_reporting_survey_publication_url($survey_publication),
	        		ToolbarItem :: DISPLAY_ICON
	        ));

            //TO DO implement survey exporter !!
            //$toolbar_data[] = array('href' => $this->browser->get_export_survey_url($survey_publication), 'label' => Translation :: get('Export'), 'img' => Theme :: get_common_image_path() . 'action_export.png');
//            $toolbar->add_item(new ToolbarItem(
//	        		Translation :: get('Move'),
//	        		Theme :: get_common_image_path() . 'action_move.png',
//	        		$this->browser->get_move_survey_publication_url($survey_publication),
//	        		ToolbarItem :: DISPLAY_ICON
//	        ));

            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('InviteParticipants'),
	        		Theme :: get_common_image_path() . 'action_invite_users.png',
	        		$this->browser->get_mail_survey_participant_url($survey_publication),
	        		ToolbarItem :: DISPLAY_ICON
	        ));

            $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('BrowseSurveyPages'),
	        		Theme :: get_common_image_path() . 'action_view_results.png',
	        		$this->browser->get_browse_survey_pages_url($survey_publication),
	        		ToolbarItem :: DISPLAY_ICON
	        ));

            if ($survey instanceof ComplexContentObjectSupport)
            {
                $toolbar->add_item(new ToolbarItem(
		        		Translation :: get('BrowseSurvey'),
		        		Theme :: get_common_image_path() . 'action_browser.png',
		        		$this->browser->get_build_survey_url($survey_publication),
		        		ToolbarItem :: DISPLAY_ICON
		        ));
            }
        }

        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('ExportToExcel'),
	        		Theme :: get_common_image_path() . 'export_excel.png',
	        		$this->browser->get_survey_publication_export_excel_url($survey_publication),
	        		ToolbarItem :: DISPLAY_ICON
	        ));

        return $toolbar->as_html();
    }
}

?>
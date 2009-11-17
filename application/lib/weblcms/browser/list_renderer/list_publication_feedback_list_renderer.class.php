<?php
/**
 * $Id: list_publication_feedback_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/list_content_object_publication_list_renderer.class.php';
/**
 * Renderer to display a list of feedback publications.
 */
class ListPublicationFeedbackListRenderer extends ListContentObjectPublicationListRenderer
{
    private $feedback;

    function ListPublicationFeedbackListRenderer($browser, $feedback)
    {
        parent :: ListContentObjectPublicationListRenderer($browser);
        $this->feedback = $feedback;
    }

    function get_publications()
    {
        return $this->feedback;
    }

    function render_up_action($publication, $first = false)
    {
        return '';
    }

    function render_down_action($publication, $last = false)
    {
        return '';
    }

    function render_visibility_action($publication)
    {
        return '';
    }

    /*function render_edit_action($publication)
	{
		$edit_url = $this->get_url(array (Tool :: PARAM_ACTION => Tool :: ACTION_EDIT, Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), 'details' => '1'), array(), true);
		$edit_link = '<a href="'.$edit_url.'"><img src="'.Theme :: get_common_image_path().'action_edit.png"  alt=""/></a>';
		return $edit_link;
	}*/
    
    /*function render_delete_action($publication)
	{
		return '';
	}*/
    
    function render_feedback_action($publication)
    {
        return '';
    }

    function render_move_to_category_action($publication)
    {
        return '';
    }

    function render_publication($publication, $first = false, $last = false)
    {
        // TODO: split into separate overrideable methods
        $html = array();
        $last_visit_date = $this->browser->get_last_visit_date();
        $icon_suffix = '';
        if ($publication->is_hidden())
        {
            $icon_suffix = '_na';
        }
        elseif ($publication->get_publication_date() >= $last_visit_date)
        {
            $icon_suffix = '_new';
        }
        
        $html[] = '<div class="feedback" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $publication->get_content_object()->get_icon_name() . $icon_suffix . '.png);">';
        $html[] = '<div class="title' . ($publication->is_visible_for_target_users() ? '' : ' invisible') . '">';
        $html[] = $this->render_title($publication);
        $html[] = '<span class="publication_info' . ($publication->is_visible_for_target_users() ? '' : ' invisible') . '">';
        $html[] = $this->render_publication_information($publication);
        $html[] = '</span>';
        $html[] = '</div>';
        $html[] = '<div class="topactions' . ($publication->is_visible_for_target_users() ? '' : ' invisible') . '">';
        $html[] = $this->render_top_action($publication);
        $html[] = '</div>';
        $html[] = '<div class="description' . ($publication->is_visible_for_target_users() ? '' : ' invisible') . '">';
        $html[] = $this->render_description($publication);
        $html[] = $this->render_attachments($publication);
        $html[] = '</div>';
        $html[] = '<div class="publication_actions">';
        $html[] = $this->render_delete_action($publication);
        $html[] = '</div>';
        $html[] = '</div>';
        return implode("\n", $html);
    }

    /**
     * Renders general publication information about the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_publication_information($publication)
    {
        $repo_viewer = $this->browser->get_user_info($publication->get_publisher_id());
        $html = array();
        $html[] = '(';
        $html[] = $this->render_repo_viewer($publication);
        $html[] = ' - ';
        $html[] = $this->render_publication_date($publication);
        $html[] = ')';
        return implode("\n", $html);
    }
}
?>
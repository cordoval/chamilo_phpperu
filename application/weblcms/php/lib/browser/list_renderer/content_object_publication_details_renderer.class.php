<?php
namespace application\weblcms;

/**
 * $Id: content_object_publication_details_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser.list_renderer
 */
require_once dirname(__FILE__) . '/../content_object_publication_list_renderer.class.php';
//require_once dirname(__FILE__) . '/list_publication_feedback_list_renderer.class.php';
require_once dirname(__FILE__) . '../../../content_object_repo_viewer.class.php';
/**
 * Renderer to display all details of learning object publication
 */
class ContentObjectPublicationDetailsRenderer extends ContentObjectPublicationListRenderer
{
//
//    function ContentObjectPublicationDetailsRenderer($browser, $parameters = array (), $actions)
//    {
//        parent :: ContentObjectPublicationListRenderer($browser, $parameters, $actions);
//    }

    /**
     * Returns the HTML output of this renderer.
     * @return string The HTML output
     */
    function as_html()
    {
        $publication_id = $this->get_tool_browser()->get_publication_id();
        $dm = WeblcmsDataManager :: get_instance();
        $publication = $dm->retrieve_content_object_publication($publication_id);
        $this->get_tool_browser()->get_parent()->set_parameter(Tool :: PARAM_PUBLICATION_ID, $publication_id);

        $html[] = '<h3>' . Translation :: get('ContentObjectPublicationDetails') . '</h3>';
        $html[] = $this->render_publication($publication);
        $html[] = '<br />';
        return implode("\n", $html);
    }

    /**
     * Renders a single publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The rendered HTML.
     */
    function render_publication($publication, $first = false, $last = false)
    {
        $html = array();
        $last_visit_date = $this->get_tool_browser()->get_last_visit_date();
        $icon_suffix = '';
        if ($publication->is_hidden())
        {
            $icon_suffix = '_na';
        }
        elseif ($publication->get_publication_date() >= $last_visit_date)
        {
            $icon_suffix = '_new';
        }

    	if($publication->get_content_object() instanceof ComplexContentObjectSupport)
        {
            $title_url = $this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_DISPLAY_COMPLEX_CONTENT_OBJECT));
        }
        
        $html[] = '<div class="announcements level_1" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $publication->get_content_object()->get_icon_name() . $icon_suffix . '.png);">';
        
        if($title_url)
        {
        	$html[] = '<a href="' . $title_url . '">';
        }
        
        $html[] = '<div class="title' . ($publication->is_visible_for_target_users() ? '' : ' invisible') . '">';
        $html[] = $this->render_title($publication);
        $html[] = '</div>';
        
		if($title_url)
		{
			$html[] = '</a>';
		}        
        
        $html[] = '<div style="padding-top: 1px;" class="description' . ($publication->is_visible_for_target_users() ? '' : ' invisible') . '">';
        $html[] = $this->render_description($publication);
        //$html[] = $this->render_attachments($publication);
        $html[] = '</div>';
        $html[] = '<div class="publication_info' . ($publication->is_visible_for_target_users() ? '' : ' invisible') . '">';
        $html[] = $this->render_publication_information($publication);
        $html[] = '</div>';
        $html[] = '<div class="publication_actions">';
        $html[] = $this->get_publication_actions($publication)->as_html();
        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }
}
?>
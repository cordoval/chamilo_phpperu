<?php
/**
 * @author Sven Vanpoucke
 * @author Nick Van Loocke
 */

class PeerAssessmentViewerWizardDisplay extends HTML_QuickForm_Action_Display
{

    private $parent;

    public function PeerAssessmentViewerWizardDisplay($parent)
    {

        $this->parent = $parent;
    }

    /**
     * Displays the HTML-code of a page in the wizard
     * @param HTML_Quickform_Page $page The page to display.
     */
    function _renderForm($current_page)
    {
        $html = array();
        //$this->parent->get_parent()->display_header();

        if ($current_page->get_page_number() != 0)
        {
            $html[] = '<div class="assessment">';
            $html[] = '<h2>' . $this->parent->get_peer_assessment()->get_title() . '</h2>';
            
	        if ($this->parent->get_peer_assessment()->has_description())
	        {
	            $html[] = '<div class="description">';
	            $html[] = $this->parent->get_peer_assessment()->get_description();
	            $html[] = '<div class="clear"></div>';
	            $html[] = '</div>';
	        }

            $html[] = '<br />';

            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';

            $html[] = '<br />';

            /*if (strlen(strip_tags($this->parent->get_peer_assessment()->get_introduction_text(), '<img>')) > 0)
            {
                $html[] = '<div class="description">';
                $introduction = $this->parent->get_peer_assessment()->get_introduction_text();

                $html[] = $this->parent->get_parent()->parse($introduction);
                $html[] = '</div>';
            }*/

            $html[] = '<br />';

            /*if (strlen(strip_tags($this->parent->get_page($current_page->get_page_number())->get_introduction_text(), '<img>')) > 0)
            {
                $html[] = '<div class="description">';
                $introduction = $this->parent->get_page($current_page->get_page_number())->get_introduction_text();

                $html[] = $this->parent->get_parent()->parse($introduction);

                $html[] = '</div>';
            }*/

            $html[] = '</div>';

            $html[] = '<div>';
            $html[] = $current_page->toHtml();
            $html[] = '</div>';

            $html[] = '<br />';

            $html[] = '<div class="assessment">';

            /*if (strlen(strip_tags($this->parent->get_page($current_page->get_page_number())->get_finish_text(), '<img>')) > 0)
            {

            	$html[] = '<div class="description">';
                $finishtext = $this->parent->get_page($current_page->get_page_number())->get_finish_text();

                $html[] = $this->parent->get_parent()->parse($finishtext);

                $html[] = '</div>';
            }*/

            $html[] = '<br />';

            /*if (strlen(strip_tags($this->parent->get_peer_assessment()->get_finish_text(), '<img>')) > 0)
            {

            	$html[] = '<div class="description">';
                $finishtext = $this->parent->get_peer_assessment()->get_finish_text();

                $html[] = $this->parent->get_parent()->parse($finishtext);
                $html[] = '</div>';
            }*/

            $html[] = '<br />';

            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = $current_page->get_page_number() . ' / ' . $this->parent->get_total_pages();
            $html[] = '</div>';

            $html[] = '</div>';

            echo implode("\n", $html);
        }
        else
        {
            $html = array();
            $html[] = '<div style="width: 100%; text-align: center;">';
            $html[] = Translation :: get('NoPeerAssessmentPageAddedToPeerAssessment');
            $html[] = '</div>';
            echo implode("\n", $html);
        }

        $this->parent->get_parent()->display_footer();

    }
}
?>
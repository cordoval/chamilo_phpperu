<?php
/**
 * $Id: browser.class.php 191 2009-11-13 11:50:28Z chellee $
 * @package application.common.feedback_manager.component
 */

/**
 * Description of browserclass
 *
 * @author Pieter Hens
 */

require_once dirname(__FILE__) . '/../feedback_form.class.php';


class FeedbackManagerBrowserComponent extends FeedbackManager
{
    
    const TITLE_MARKER = '<!-- /title -->';
    const DESCRIPTION_MARKER = '<!-- /description -->';
    
    private $html;

    function run()
    {
        $html = $this->as_html();
        
    	$this->display_header();
    	echo $html;
    	$this->display_footer();
    }

    function as_html()
    {
        $application = $this->get_application();
        $publication_id = $this->get_publication_id();
        $complex_wrapper_id = $this->get_complex_wrapper_id();
        $action = $this->get_action();
        $html = array();

        if($action == FeedbackManager::ACTION_BROWSE_ONLY_FEEDBACK || $action == FeedbackManager::ACTION_CREATE_ONLY_FEEDBACK)
        {
            //don't show the standard quick feedback form, only show the feedback!
            $html[] = '<h3>' . Translation :: get('PublicationFeedback') . '</h3>';
            
                $feedbackpublications = $this->retrieve_feedback_publications($publication_id, $complex_wrapper_id, $application);
                $feedback_count = AdminDataManager :: get_instance()->count_feedback_publications($publication_id, $complex_wrapper_id, $application);
                $counter = 0;
                while ($feedback = $feedbackpublications->next_result())
                {
                    $counter ++;
                    if ($counter == 4)
                    {
                        $html[] = '<br /><a href="#" id="showfeedback" style="display:none; float:left;">' . Translation :: get('ShowAllFeedback') . '[' . ($feedback_count - 3) . ']</a><br><br>';
                        $html[] = '<a href="#" id="hidefeedback" style="display:none; font-size: 80%; font-weight: normal;">(' . Translation :: get('HideFeedback') . ')</a>';
                        $html[] = '<div id="feedbacklist">';
                    }
                    $html[] = $this->render_feedback($feedback);
                }
                if ($counter > 3)
                {
                    $html[] = '</div>';
                }
               
                $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/feedback_list.js' . '"></script>';

        }
        else
        {

            $form = new FeedbackManagerForm($this->get_url());

            if ($form->validate())
            {
                $success = $form->create_feedback($this->get_user()->get_id(), $publication_id, $complex_wrapper_id, $application);
                $this->redirect($success ? "" : Translation :: get('FeedbackNotCreated'), $success ? null : true, array());

            }
            else
            {
                $html[] = '<h3>' . Translation :: get('Feedback') . '</h3>';
                $this->render_create_action();

                $feedbackpublications = $this->retrieve_feedback_publications($publication_id, $complex_wrapper_id, $application);
                $feedback_count = AdminDataManager :: get_instance()->count_feedback_publications($publication_id, $complex_wrapper_id, $application);


                $counter = 0;
                while ($feedback = $feedbackpublications->next_result())
                {
                    $counter ++;

                    if ($counter == 4)
                    {
                        $html[] = '<br /><a href="#" id="showfeedback" style="display:none; float:left;">' . Translation :: get('ShowAllFeedback') . '[' . ($feedback_count - 3) . ']</a><br><br>';
                        $html[] = '<a href="#" id="hidefeedback" style="display:none; font-size: 80%; font-weight: normal;">(' . Translation :: get('HideAllFeedback') . ')</a>';
                        $html[] = '<div id="feedbacklist">';
                    }
                    $html[] = $this->render_feedback($feedback);

                }

                if ($counter > 3)
                {
                    $html[] = '</div>';
                }

                $html[] = $form->toHtml();

                $html[] = '<script type="text/javascript" src="' . Path :: get(WEB_LIB_PATH) . 'javascript/feedback_list.js' . '"></script>';
            }
        }
        
        $this->html = $html;
        
        return implode("\n", $this->html);
    }

    function render_feedback($feedback)
    {
        
        $id = $feedback->get_fid();
        $feedback_object = RepositoryDataManager :: get_instance()->retrieve_content_object($id);
        $html = array();
        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $feedback_object->get_icon_name() . ($feedback_object->is_latest_version() ? '' : '_na') . '.png);">';
        $html[] = '<div class="title">' . Utilities :: htmlentities($feedback_object->get_title());
        $html[] = '<span class="publication_info">';
        $html[] = $this->render_publication_information($feedback_object);
        $html[] = '</span>';
        $html[] = '</div>';
        $html[] = self :: TITLE_MARKER;
        $html[] = $this->get_description($feedback_object);
        
        if ($this->get_user()->get_id() == $feedback_object->get_owner_id())
        {
            $html[] = '<div class="publication_actions">';
            $html[] = $this->render_delete_action($feedback);
            $html[] = $this->render_update_action($feedback);
            $html[] = '</div>';
        }
        
        $html[] = '</div>';
        
        return implode("\n", $html);
    
    }

    function get_description($feedback)
    {
        $display = ContentObjectDisplay :: factory($feedback);
        $description = $display->get_description();
        $parsed_description = BbcodeParser :: get_instance()->parse($description);
        return '<div class="description">' . $parsed_description . '</div>';
    }

    function render_delete_action($feedback)
    {
        $delete_url = $this->get_url(array(FeedbackManager :: PARAM_ACTION => FeedbackManager :: ACTION_DELETE_FEEDBACK,  FeedbackManager :: PARAM_FEEDBACK_ID => $feedback->get_id()));
        $delete_link = '<a href="' . $delete_url . '" onclick="return confirm(\'' . addslashes(Translation :: get('ConfirmYourChoice')) . '\');"><img src="' . Theme :: get_common_image_path() . 'action_delete.png"  alt=""/></a>';
        return $delete_link;
    }

    function render_update_action($feedback)
    {
        $update_url = $this->get_url(array(FeedbackManager :: PARAM_ACTION => FeedbackManager :: ACTION_UPDATE_FEEDBACK, FeedbackManager :: PARAM_FEEDBACK_ID => $feedback->get_id()));
        $update_link = '<a href="' . $update_url . '"><img src="' . Theme :: get_common_image_path() . 'action_edit.png"  alt=""/></a>';
        return $update_link;
    }

    function render_create_action()
    {
        $create_url = $this->get_url(array(FeedbackManager :: PARAM_ACTION => FeedbackManager :: ACTION_CREATE_FEEDBACK));
        $item = new ToolbarItem(Translation :: get('CreateFeedback'), Theme :: get_common_image_path() . 'action_create.png', $create_url, ToolbarItem :: DISPLAY_ICON_AND_LABEL);
        $this->get_parent()->add_actionbar_item($item);
    }

    function render_publication_information($feedback)
    {
        $user = UserManager :: retrieve_user($feedback->get_owner_id());
        $html = array();
        $html[] = '(';
        $html[] = $user->get_lastname();
        ;
        $html[] = $user->get_firstname();
        ;
        $html[] = ' - ';
        $html[] = $this->format_date($feedback->get_creation_date());
        $html[] = ')';
        return implode("\n", $html);
    }

    function format_date($date)
    {
        $date_format = Translation :: get('dateTimeFormatLong');
        return DatetimeUtilities :: format_locale_date($date_format, $date);
    }

}
?>
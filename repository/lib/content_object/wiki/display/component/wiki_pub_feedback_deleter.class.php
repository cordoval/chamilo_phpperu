<?php
/**
 * $Id: wiki_pub_feedback_deleter.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component
 */

class WikiDisplayWikiPubFeedbackDeleterComponent extends WikiDisplay
{
    private $complex_id;
    private $wiki_publication_id;

    function run()
    {
        if ($this->is_allowed(DELETE_RIGHT))
        {
            if (Request :: get(WikiPubFeedback :: PROPERTY_FEEDBACK_ID))
                $feedback_ids = Request :: get(WikiPubFeedback :: PROPERTY_FEEDBACK_ID);
            else
                $feedback_ids = $_POST[WikiPubFeedback :: PROPERTY_FEEDBACK_ID];
                
            if (Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID))
                $this->complex_id = Request :: get(ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID);
            else
                $this->complex_id = $_POST[ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID];
            
            if (Request :: get('wiki_publication'))
                $this->wiki_publication_id = Request :: get('wiki_publication');
            else
                $this->wiki_publication_id = $_POST['wiki_publication'];
            
            if (! is_array($feedback_ids))
            {
                $feedback_ids = array($feedback_ids);
            }
            
            $datamanager = WikiDataManager :: get_instance();
            $errors = 0;
            foreach ($feedback_ids as $index => $feedback_id)
            {
                $condition = new EqualityCondition(WikiPubFeedback :: PROPERTY_FEEDBACK_ID, $feedback_id);
                $feedbacks = $datamanager->retrieve_wiki_pub_feedbacks($condition);
                while ($feedback = $feedbacks->next_result())
                {
                    if(!$feedback->delete())
                        $errors++;
                }
            }
            if (count($feedback_ids) > 1)
            {
                if(!$errors)
                    $message = htmlentities(Translation :: get('ContentObjectFeedbacksDeleted'));
                else
                    $message = htmlentities(Translation :: get('ContentObjectFeedbacksNotDeleted'));
            }
            else
            {
                if(!$errors)
                    $message = htmlentities(Translation :: get('ContentObjectFeedbackDeleted'));
                else
                    $message = htmlentities(Translation :: get('ContentObjectFeedbackNotDeleted'));
            }
            
            $this->redirect($message, ($errors ? true : false), array(Tool :: PARAM_ACTION => Request :: get('tool_action'), 'display_action' => 'discuss', 'wiki_publication' => $this->wiki_publication_id, ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID => $this->complex_id));
        }
    }

}
?>
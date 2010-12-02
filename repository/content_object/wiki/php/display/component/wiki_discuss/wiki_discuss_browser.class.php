<?php
namespace repository\content_object\wiki;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;

use repository\ContentObject;
use application\weblcms\ContentObjectPubFeedbackBrowser;
use application\weblcms\ListContentObjectPublicationListRenderer;

/**
 * $Id: wiki_discuss_browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.wiki.component.wiki_discuss
 */


/**
 * Browser to allow the user to view the published feedback on a wiki page
 */
class WikiDiscussBrowser extends ContentObjectPubFeedbackBrowser
{
    private $feedbacks;

    function __construct($parent)
    {
        parent :: __construct($parent, 'wiki');

        $renderer = new ListContentObjectPublicationListRenderer($this);
        $actions = array(Tool :: ACTION_DELETE => Translation :: get('DeleteSelected', null , Utilities :: COMMON_LIBRARIES), Tool :: ACTION_HIDE => Translation :: get('Hide', null , Utilities :: COMMON_LIBRARIES), Tool :: ACTION_SHOW => Translation :: get('Show', null , Utilities :: COMMON_LIBRARIES));
        $renderer->set_actions($actions);

        $this->set_publication_list_renderer($renderer);

        function get_publications($from, $count, $column, $direction)
        {
            if (empty($this->feedbacks))
            {
                $datamanager = RepositoryDataManager :: get_instance();
                $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_PUBLICATION_ID, $this->publication_id);
                $conditions[] = new EqualityCondition(ContentObjectPubFeedback :: PROPERTY_COMPLEX_CONTENT_OBJECT_ITEM_ID, $this->complex_id);
                if ($this->get_parent()->get_condition())
                    $conditions[] = $this->get_parent()->get_condition();
                $condition = new AndCondition($conditions);
                $feedbacks = $datamanager->retrieve_content_object_pub_feedback($condition);
                while ($feedback = $feedbacks->next_result())
                {
                    // If the feedback is hidden and the user is not allowed to DELETE or EDIT, don't show this publication
                    if (! ($this->is_allowed(DELETE_RIGHT) || $this->is_allowed(EDIT_RIGHT)))
                    {
                        continue;
                    }
                    $visible_feedbacks[] = $feedback;
                }

                $this->feedbacks = $visible_feedbacks;
            }

            return $this->feedbacks;

        }

        function get_publication_count()
        {
            return count($this->get_publications());
        }
    }
}
?>
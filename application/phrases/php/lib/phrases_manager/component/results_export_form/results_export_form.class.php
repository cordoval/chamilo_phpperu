<?php
namespace application\phrases;

use common\libraries\Translation;
use common\libraries\FormValidator;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use common\libraries\Theme;
use common\libraries\Export;
use common\libraries\Utilities;

use user\UserDataManager;

/**
 * @author Hans De Bisschop
 * @package application.phrases
 */

class PhrasesResultsExportForm extends FormValidator
{

    function __construct($url)
    {
        parent :: __construct('phrases', 'post', $url);
        $this->initialize();
    }

    function initialize()
    {
        if (Request :: get('tid'))
        {
            $tid = Request :: get('tid');
            $track = new PhrasesAdaptiveAssessmentAttemptTracker();
            $condition = new EqualityCondition(PhrasesAdaptiveAssessmentAttemptTracker :: PROPERTY_ID, $tid);
            $uass = $track->retrieve_tracker_items($condition);
            $user_phrases = $uass[0];

            $publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($user_phrases->get_phrases_id());
            $phrases = $publication->get_publication_object();
            $user = UserDataManager :: get_instance()->retrieve_user($user_phrases->get_user_id());

            //$this->addElement('html', '<h3>Phrases: '.$phrases->get_title().'</h3><br/>');
            $this->addElement('html', '<h3>Export results for user ' . $user->get_fullname() . '</h3><br />');

            $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/phrases.png);">';
            $html[] = '<div class="title">';
            $html[] = $phrases->get_title();
            $html[] = '</div>';
            $html[] = $phrases->get_description();
            $html[] = '</div><br />';

            $this->addElement('html', implode("\n", $html));
        }
        else
//            if (Request :: get(PhrasesTool :: PARAM_PUBLICATION_ID))
//            {
//                $aid = Request :: get(PhrasesTool :: PARAM_PUBLICATION_ID);
//                $publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($aid);
//
//                $this->addElement('html', '<h3>Phrases: ' . $publication->get_content_object()->get_title() . '</h3><br/>');
//                $this->addElement('html', '<h3>Export results for user ' . $user->get_fullname() . '</h3><br />');
//            }

        $options = Export :: get_supported_filetypes(array('ical'));
        $this->addElement('select', 'filetype', 'Export to filetype:', $options);
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Export', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive export'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>
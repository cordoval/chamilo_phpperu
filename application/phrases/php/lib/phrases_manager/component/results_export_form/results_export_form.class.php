<?php
namespace application\phrases;

use common\libraries\FormValidator;
use common\libraries\Request;
use common\libraries\EqualityCondition;
use user\UserDataManager;
use common\libraries\Theme;
use common\libraries\Export;
use common\libraries\Utilities;
/**
 * $Id: results_export_form.class.php 193 2009-11-13 11:53:37Z chellee $
 * @package application.lib.phrases.phrases_manager.component.results_export_form
 */

require_once dirname(__FILE__) . '/../../../../trackers/phrases_phrases_attempts_tracker.class.php';

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
            $track = new PhrasesPhrasesAttemptsTracker();
            $condition = new EqualityCondition(PhrasesPhrasesAttemptsTracker :: PROPERTY_ID, $tid);
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
            if (Request :: get(PhrasesTool :: PARAM_PUBLICATION_ID))
            {
                $aid = Request :: get(PhrasesTool :: PARAM_PUBLICATION_ID);
                $publication = PhrasesDataManager :: get_instance()->retrieve_phrases_publication($aid);

                $this->addElement('html', '<h3>Phrases: ' . $publication->get_content_object()->get_title() . '</h3><br/>');
                $this->addElement('html', '<h3>Export results for user ' . $user->get_fullname() . '</h3><br />');
            }

        $options = Export :: get_supported_filetypes(array('ical'));
        $this->addElement('select', 'filetype', 'Export to filetype:', $options);
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Export', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive export'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>
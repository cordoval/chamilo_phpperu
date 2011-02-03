<?php

namespace repository\content_object\twitter_search;

use common\libraries\Translation;
use repository\ContentObjectForm;

//require_once dirname(__FILE__) . '/rss_feed.class.php';

/**
 *
 * @copyright (c) 2011 University of Geneva
 * @license GNU General Public License - http://www.gnu.org/copyleft/gpl.html
 * @author laurent opprecht
 * @package repository.lib.content_object.twitter_search
 */
class TwitterSearchForm extends ContentObjectForm {

    protected function build_creation_form() {
        parent :: build_creation_form();
        $this->addElement('category', Translation :: get('Properties'));
        $this->add_textfield(TwitterSearch :: PROPERTY_QUERY, Translation :: get('Query'), true, ' size="100" style="width: 100%;"');
        $this->addElement('category');
    }

    protected function build_editing_form() {
        parent :: build_editing_form();
        $this->addElement('category', Translation :: get('Properties'));

        $this->add_textfield(TwitterSearch :: PROPERTY_QUERY, Translation :: get('Query'), true, ' size="100" style="width: 100%;"');
        $this->addElement('static', '', '', '<a href="http://search.twitter.com/operators" target="_blank">' . Translation::get('Help') . '</a>');
       
        $this->addElement('category');
    }

    function setDefaults($defaults = array()) {
        $lo = $this->get_content_object();
        if (isset($lo)) {
            $defaults[TwitterSearch :: PROPERTY_QUERY] = $lo->get_query();
        } else {
            $defaults[TwitterSearch :: PROPERTY_QUERY] = '';
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object() {
        $content_object = new TwitterSearch();
        $content_object->set_query($this->exportValue(TwitterSearch :: PROPERTY_QUERY));
        $this->set_content_object($content_object);
        return parent :: create_content_object();
    }

    function update_content_object() {
        $content_object = $this->get_content_object();
        $content_object->set_query($this->exportValue(TwitterSearch :: PROPERTY_QUERY));
        return parent :: update_content_object();
    }

}

?>
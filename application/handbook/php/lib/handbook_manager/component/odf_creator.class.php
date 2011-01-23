<?php

namespace application\handbook;

use Odf;
use common\libraries\Path;
use repository\RepositoryManager;
use repository\content_object\handbook_topic\HandbookTopic;
use repository\content_object\handbook\Handbook;
use repository\content_object\handbook_item\HandbookItem;
use common\libraries\EqualityCondition;
use repository\ComplexContentObjectItem;
use repository\RepositoryDataManager;
use common\libraries\Request;

require_once dirname(__FILE__) . '/../../../../../../common/libraries/plugin/odtPHP/library/odf.php';
require_once dirname(__FILE__) . '/../handbook_manager.class.php';

/**
 * Handbook component to create odf documents from handbook-information
 * @author Nathalie Blocry
 */
class HandbookManagerOdfCreatorComponent extends HandbookManager {

    public $max_level = 9;

    function run() {
        $template_file = dirname(__FILE__) . '/handbook_odf_creator/handbook_template.odt';

        //GET CONTENT
        $rdm = RepositoryDataManager::get_instance();
        $handbook_id = Request :: get(HandbookManager::PARAM_HANDBOOK_ID);
        $handbook_publication_id = Request :: get(HandbookManager::PARAM_HANDBOOK_PUBLICATION_ID);
        $top_handbook = $rdm->retrieve_content_object($handbook_id);
        $content_array = $this->handbookToArray($top_handbook, $handbook_publication_id);
        $text_array = implode(",", $content_array);


        //BUILD FILE WITH CONTENT
        $odf = new Odf($template_file);

        $odf->setVars('title', $content_array['title']);
        $odf->setVars('message', $content_array['description']);

        $i = 1;

        $topics = $odf->setSegment('level1');

        foreach ($content_array['items'] as $sub_item) {
            $topics->title($sub_item['title']);
            $topics->content($sub_item['content']);
            if ($sub_item['image'] != null) {
                $topics->setImage('image', $sub_item['image']);
            } else {
                $topics->setVars('image', '');
            }

            $o = '2';
            $this->removePlaceholders($o, $topics);
            $topics->merge();
        }
        foreach ($content_array['subhandbooks'] as $sub_handbook) {
            $topics->title($sub_handbook['title']);
            $topics->content($sub_handbook['description']);
            $topics->setVars('image', '');

            $o = '2';
            $this->addItems($o, $topics, $sub_handbook['items']);
            $this->add_subhandbooks($o, $topics, $sub_handbook['subhandbooks']);
            $topics->merge();
            $i++;
        }
        $odf->mergeSegment($topics);

        $odf->exportAsAttachedFile();
    }

    function add_subhandbooks($i, $segment, $contentArray) {
        if ($contentArray == nul || count($contentArray) < 1) {
            $this->removePlaceholders($i, $segment);
        } else {
            $level = 'level' . $i;

            foreach ($contentArray as $topic) {
                $segment->$level->setVars('title' . $i, html_entity_decode(strip_tags($topic['title'])));
                $segment->$level->setVars('content' . $i, html_entity_decode(strip_tags($topic['content'])));
                $segment->$level->setVars('image' . $i, '');

                if (($topic['subhandbooks'] != null) && (count($topic['subhandbooks'] > 0)) && ($i <= $this->max_level)) {
                    $this->add_subhandbooks($i + 1, $segment->$level, $topic['subhandbooks']);
                }
                if (($topic['items'] != null) && (count($topic['items'] > 0)) && ($i <= $this->max_level)) {
                    $this->add_items($i + 1, $segment->$level, $topic['items']);
                }

                $segment->$level->merge();
            }
        }
    }

    /**
     * Function to add topics to a odf document
     * @param <type> $i: level of the topic
     * @param <type> $segment: segment to add the topics to
     * @param <type> $contentArray: array containing an array with 'title', 'content', and 'image' values for each topic
     */
    function addItems($i, $segment, $contentArray) {
        $level = 'level' . $i;
        if ($contentArray == nul || count($contentArray) < 1) {
            $this->removePlaceholders($i, $segment);
        } else {
            foreach ($contentArray as $topic) {
                $segment->$level->setVars('title' . $i, html_entity_decode(strip_tags($topic['title'])));
                $segment->$level->setVars('content' . $i, html_entity_decode(strip_tags($topic['content'])));
                if ($topic['image'] != null) {
                    $segment->$level->setImage('image' . $i, $topic['image']);
                } else {
                    //topic has no picture
                    //image tag needs to be removed
                    $segment->$level->setVars('image' . $i, '');
                }
                $segment->$level->merge();
            }
        }
    }

    /**
     * function to remove the placeholders when no content for the level exists
     * @param <type> $i
     * @param <type> $segment
     */
    function removePlaceholders($i, $segment) {
        $level = 'level' . $i;
//           $segment->$level->delete();
        $segment->$level->setVars('title' . $i, '');
        $segment->$level->setVars('content' . $i, '');
        $segment->$level->setVars('image' . $i, '');

        $segment->$level->merge();
    }

    function handbookToArray($handbook, $publication_id) {
        if ($handbook->get_type() == Handbook :: get_type_name()) {

            $handbook_content = array();
            $alternatives = HandbookManager::get_alternatives_preferences_types($handbook->get_id(), $publication_id);

            $handbook_content['title'] = html_entity_decode(strip_tags($alternatives['handbook_main']->get_title()));
            $handbook_content['description'] = trim(html_entity_decode(strip_tags($alternatives['handbook_main']->get_description())));

            $rdm = RepositoryDataManager :: get_instance();
            $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $handbook->get_id(), ComplexContentObjectItem :: get_table_name()));
            while ($child = $children->next_result()) {
                $lo = $rdm->retrieve_content_object($child->get_ref());
                $item = array();
                if ($lo->get_type() == HandbookItem::get_type_name()) {
                    $lo = $rdm->retrieve_content_object($lo->get_reference());
                }
                if ($lo->get_type() == Handbook :: get_type_name()) {
                    //handbook
                    $handbook_content['subhandbooks'][] = $this->handbookToArray($lo);
                } else {
                    //handbook_topic
                    $handbook_content['items'][] = $this->topicToArray($lo);
                }
            }

            return $handbook_content;
        } else {
            return null;
        }
    }

    function topicToArray($topic, $publication_id) {
        if ($topic->get_type() == HandbookTopic::get_type_name()) {
            $alternatives = HandbookManager::get_alternatives_preferences_types($topic->get_id(), $publication_id);
            $topic_content = array();

            if ($alternatives['text_main'] != null) {
                $topic_content['title'] = trim(html_entity_decode(strip_tags($alternatives['text_main']->get_title())));
                $topic_content['content'] = trim(html_entity_decode(strip_tags($alternatives['text_main']->get_text())));
            }

            if ($alternatives['image_main'] != null) {
                $image = $alternatives['image_main'];
                $url = $image->get_full_path();
                $topic_content['image'] = $url;
            }


            return $topic_content;
        } else {
            return null;
        }
    }

}

?>
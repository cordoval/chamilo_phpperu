<?php
/**
 * $Id: question_qti_import.class.php 204 2009-11-13 12:51:30Z kariboe $
 * @package repository.lib.import.qti.question
 */
require_once dirname(__FILE__) . '/question_types/document_import.class.php';
require_once dirname(__FILE__) . '/question_types/fill_in_blanks_import.class.php';
require_once dirname(__FILE__) . '/question_types/matching_import.class.php';
require_once dirname(__FILE__) . '/question_types/multiple_answer_import.class.php';
require_once dirname(__FILE__) . '/question_types/multiple_choice_import.class.php';
require_once dirname(__FILE__) . '/question_types/open_question_import.class.php';
require_once dirname(__FILE__) . '/question_types/hotspot_question_import.class.php';
require_once dirname(__FILE__) . '/question_types/open_question_with_document_import.class.php';
require_once dirname(__FILE__) . '/question_types/rating_import.class.php';
require_once dirname(__FILE__) . '/question_types/match_import.class.php';
require_once dirname(__FILE__) . '/question_types/matrix_import.class.php';
require_once dirname(__FILE__) . '/question_types/ordering_import.class.php';

class QuestionQtiImport extends QtiImport
{

    function import_content_object()
    {
        $importer = $this->factory_qti_question($this->get_content_object_file(), $this->get_user(), $this->get_category());
        if ($importer)
            return $importer->import_content_object();
    }

    function factory_qti_question($lo_file, $user, $category)
    {
        $data = $this->get_file_content_array();
        $itembody = $data['itemBody'];
        foreach ($itembody as $key => $itemdata)
        {
            $tag_type = substr($key, (strlen($key) - strlen('Interaction')), strlen($key));
            if ($tag_type == 'Interaction')
            {
                $tag = $key;
                //options needed to differentiate between question types with the same tag
                $num_choices = $itemdata['maxChoices'];
                $ubound = $itemdata['upperBound'];
                break;
            }
            if ($key == 'blockquote')
            {
                if ($itemdata['p']['textEntryInteraction'] != null || $itemdata['textEntryInteraction'] != null)
                {
                    $tag = 'textEntryInteraction';
                    break;
                }
            }
        }
        switch ($tag)
        {
            case 'extendedTextInteraction' :
                return new OpenQuestionQtiImport($lo_file, $user, $category);
            case 'uploadInteraction' :
                return new DocumentQuestionQtiImport($lo_file, $user, $category);
            case 'choiceInteraction' :
                if ($num_choices == 1)
                    return new MultipleChoiceQuestionQtiImport($lo_file, $user, $category);
                else
                    return new MultipleAnswerQuestionQtiImport($lo_file, $user, $category);
            case 'sliderInteraction' :
                return new RatingQuestionQtiImport($lo_file, $user, $category);
            case 'textEntryInteraction' :
                return new FillInBlanksQuestionQtiImport($lo_file, $user, $category);
            case 'matchInteraction' :
                return new MatchingQuestionQtiImport($lo_file, $user, $category);
            case 'graphicOrderInteraction' :
                return new HotspotQuestionQtiImport($lo_file, $user, $category);
            case 'orderInteraction' :
                return new OrderingQuestionQtiImport($lo_file, $user, $category);
            default :
                return null;
        }
    }

    function import_images($text)
    {
        $tags = Text :: fetch_tag_into_array($text, '<img>');
        $new_dir = Path :: get(REL_PATH) . 'files/repository/' . $this->get_user()->get_id() . '/';
        
        if (! file_exists($temp_dir))
        {
            mkdir($temp_dir, null, true);
        }
        
        foreach ($tags as $tag)
        {
            $parts = split('/', $tag['src']);
            $newfilename = $new_dir . $parts[count($parts) - 1];
            $files[$newfilename] = $tag['src'];
            $text = str_replace($tag['src'], $newfilename, $text);
        }
        $orig_path = dirname($this->get_content_object_file());
        foreach ($files as $new => $original)
        {
            copy($orig_path . '/' . $original, $new);
        }
        return $text;
    }

    function get_tag_content($tagname, $params = array())
    {
        $doc = new DOMDocument();
        $doc->load(parent :: get_content_object_file());
        $elems = $doc->getElementsByTagName($tagname);
        
        if (! is_array($params))
        {
            $xmltag = $elems->item($params);
        }
        else
        {
            foreach ($elems as $elem)
            {
                $valid = true;
                foreach ($params as $attr => $value)
                {
                    if ($elem->getAttribute($attr) != $value)
                    {
                        $valid = false;
                    }
                }
                if ($valid)
                {
                    $xmltag = $elem;
                    break;
                }
            }
        }
        
        if ($xmltag)
        {
            $tag = $xmltag->C14N();
            $index = stripos($tag, '>');
            $tag = substr($tag, $index + 1);
            $tag = substr($tag, 0, strlen($tag) - strlen('</' . $tagname . '>'));
            return $tag;
        }
    }

    function create_question($question)
    {
        //dump($question);
        $question->set_owner_id($this->get_user()->get_id());
        $question->set_parent_id(0);
        return $question->create();
    }

}
?>
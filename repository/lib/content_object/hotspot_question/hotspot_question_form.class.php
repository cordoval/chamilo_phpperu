<?php
/**
 * $Id: hotspot_question_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.hotspot_question
 */
require_once dirname(__FILE__) . '/hotspot_question.class.php';
require_once dirname(__FILE__) . '/hotspot_question_answer.class.php';
/**
 * This class represents a form to create or update hotspot questions
 */
class HotspotQuestionForm extends ContentObjectForm
{

    //private $colours = array('#00315b', '#00adef', '#aecee7', '#9dcfc3', '#016c62', '#c7ac21', '#ff5329', '#bd0019', '#e7ad7b', '#bd0084', '#9d8384', '#42212a', '#005b84', '#e0eeef', '#00ad9c', '#ffe62a', '#f71932', '#ff9429', '#f6d7c5', '#7a2893');
    private $colours = array('#ff0000', '#f2ef00', '#00ff00', '#00ffff', '#0000ff', '#ff00ff', '#0080ff', '#ff0080', '#00ff80', '#ff8000', '#8000ff');

    protected function build_creation_form()
    {
        parent :: build_creation_form();

        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.draw.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/hotspot_question_form.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/uploadify2/swfobject.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/uploadify2/jquery.uploadify.v2.1.0.min.js'));

        $url = $this->get_path(WEB_PATH) . 'repository/xml_feeds/xml_image_feed.php';
        $locale = array();
        $locale['Display'] = Translation :: get('AddAttachments');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');

        //$this->add_warning_message('hotspot_javascript', Translation :: get('HotspotJavascriptWarning'), Translation :: get('HotspotJavascriptRequired'), true);

        $this->addElement('html', '<div id="hotspot_select" style="display: none;">');
        $this->addElement('category', Translation :: get(get_class($this) . 'Hotspots'));
        $this->addElement('static', 'uploadify', Translation :: get('UploadImage'), '<div id="uploadify"></div>');
        $this->addElement('element_finder', 'image', Translation :: get('SelectImage'), $url, $locale, array());
        $this->addElement('category');
        $this->addElement('html', '</div>');

        $this->addElement('html', '<div id="hotspot_options" style="display: none;">');
        $this->addElement('category', Translation :: get(get_class($this) . 'Hotspots'));
        $this->add_options();
        $this->addElement('hidden', 'image_object', Translation :: get('ImageObject'));
        $this->addElement('category');

        $this->add_image();
        $this->addElement('html', '</div>');

        $this->set_session_answers();
    }

    protected function build_editing_form()
    {
        parent :: build_creation_form();

        $this->addElement('category', Translation :: get(get_class($this) . 'Properties'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/jquery.draw.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/serializer.pack.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/hotspot_question_form.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/uploadify2/swfobject.js'));
        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PLUGIN_PATH) . 'jquery/uploadify2/jquery.uploadify.v2.1.0.min.js'));
        $this->add_options();
        $this->addElement('hidden', 'image_object', Translation :: get('ImageObject'));

        $this->addElement('category');

        $this->add_image();
        $this->set_session_answers();
    }

    function setDefaults($defaults = array ())
    {
        if (! $this->isSubmitted())
        {
            $object = $this->get_content_object();
            if (! is_null($object))
            {
                $answers = $object->get_answers();
                foreach ($answers as $i => $answer)
                {
                    $defaults['answer'][$i] = $answer->get_answer();
                    $defaults['comment'][$i] = $answer->get_comment();
                    $defaults['coordinates'][$i] = $answer->get_hotspot_coordinates();
                    $defaults['option_weight'][$i] = $answer->get_weight();
                }

                for($i = count($answers); $i < $_SESSION['mc_number_of_options']; $i ++)
                {
                    $defaults['option_weight'][$i] = 1;
                }

                $defaults['image_object'] = $object->get_image();
                $this->set_session_answers($defaults);
            }
	        else
	        {
	            $number_of_options = intval($_SESSION['mc_number_of_options']);

	            for($option_number = 0; $option_number < $number_of_options; $option_number ++)
	            {
	                $defaults['option_weight'][$option_number] = 1;
	            }
	        }
        }
        parent :: setDefaults($defaults);
    }

    function create_content_object()
    {
        $values = $this->exportValues();
        $object = new HotspotQuestion();
        $object->set_image($values['image_object']);
        $this->set_content_object($object);
        $this->add_options_to_object();
        $success = parent :: create_content_object();

        if ($success)
        {
            $object->attach_content_object($values['image_object']);
        }

        return $object;
    }

    function update_content_object()
    {
        $this->add_options_to_object();
        unset($_SESSION['web_path']);
        unset($_SESSION['hotspot_path']);
        return parent :: update_content_object();
    }

    private function add_options_to_object()
    {
        $object = $this->get_content_object();
        $object->set_answers('');
        $values = $this->exportValues();
        $answers = $values['answer'];
        $comments = $values['comment'];
        $coordinates = $values['coordinates'];
        $weights = $values['option_weight'];
        for($i = 0; $i < $_SESSION['mc_number_of_options']; $i ++)
        {
        	if(!in_array($i,$_SESSION['mc_skip_options']))
        	{
            	$answer = new HotspotQuestionAnswer($answers[$i], $comments[$i], $weights[$i], $coordinates[$i]);
            	$object->add_answer($answer);
        	}
        }
    }

    //    function validate()
    //    {
    //        if (isset($_POST['add']) || isset($_POST['remove']) || isset($_POST['fileupload']))
    //        {
    //            return false;
    //        }
    //        return parent :: validate();
    //    }


    function set_session_answers($defaults = array())
    {
        if (count($defaults) == 0)
        {
            $answers = $_POST['answer'];
            $weights = $_POST['option_weight'];
            $coords = $_POST['coordinates'];

            $_SESSION['answers'] = $answers;
            $_SESSION['option_weight'] = $weights;
            $_SESSION['coordinates'] = $coords;
        }
        else
        {
            $_SESSION['answers'] = $defaults['answer'];
            $_SESSION['weights'] = $defaults['weight'];
            $_SESSION['coordinates'] = $defaults['coordinates'];
        }
    }

    function add_image()
    {
        $object = $this->get_content_object();

        $this->addElement('category', Translation :: get('HotspotImage'));

        $html = array();
        $html[] = '<div id="hotspot_marking"><div class="colour_box_label">' . Translation :: get('CurrentlyMarking') . '</div><div class="colour_box"></div></div>';
        $html[] = '<div class="clear"></div>';
        $html[] = '<br />';
        $html[] = '<div class="clear"></div>';

        if (! is_null($object))
        {
            $image_id = $object->get_image();
            $image_object = RepositoryDataManager :: get_instance()->retrieve_content_object($image_id);

            $dimensions = getimagesize($image_object->get_full_path());
            $html[] = '<div id="hotspot_container"><div id="hotspot_image" style="width: ' . $dimensions[0] . 'px; height: ' . $dimensions[1] . 'px; background-image: url(' . $image_object->get_url() . ')"></div></div>';
        }
        else
        {
            $html[] = '<div id="hotspot_container"><div id="hotspot_image"></div></div>';
        }

//        $html[] = '<div class="clear"></div>';
//        $html[] = '<button id="change_image" class="negative delete">' . htmlentities(Translation :: get('SelectAnotherImage')) . '</button>';

        $this->addElement('html', implode("\n", $html));
        $this->addElement('category');
    }

    /**
     * Adds the form-fields to the form to provide the possible options for this
     * multiple choice question
     */
    private function add_options()
    {
        if (! $this->isSubmitted())
        {
            unset($_SESSION['mc_number_of_options']);
            unset($_SESSION['mc_skip_options']);
        }
        if (! isset($_SESSION['mc_number_of_options']) || $_SESSION['mc_number_of_options'] < 1)
        {
            $_SESSION['mc_number_of_options'] = 1;
        }
        if (! isset($_SESSION['mc_skip_options']))
        {
            $_SESSION['mc_skip_options'] = array();
        }
        if (isset($_POST['add']))
        {
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] + 1;
        }
        if (isset($_POST['remove']))
        {
            /*$indexes = array_keys($_POST['remove']);
			if (!in_array($indexes[0],$_SESSION['mc_skip_options']))
				$_SESSION['mc_skip_options'][] = $indexes[0];*/
            $indexes = array_keys($_POST['remove']);
            $_SESSION['mc_number_of_options'] = $_SESSION['mc_number_of_options'] - 1;
            //$this->move_answer_arrays($indexes[0]);
        }
        $object = $this->get_content_object();
        if (! $this->isSubmitted() && $object->get_number_of_options() != 0)
        {
            $_SESSION['mc_number_of_options'] = $object->get_number_of_answers();
            //$_SESSION['mc_answer_type'] = $object->get_answer_type();
        }
        $number_of_options = intval($_SESSION['mc_number_of_options']);

        if (isset($_SESSION['file']))
        {
            $this->addElement('html', '<div class="content_object">');
            $this->addElement('html', '</div>');
        }

        $this->addElement('hidden', 'mc_number_of_options', $_SESSION['mc_number_of_options'], array('id' => 'mc_number_of_options'));

        $buttons = array();
        $buttons[] = $this->createElement('style_button', 'add[]', Translation :: get('AddHotspotOption'), array('class' => 'normal add add_option'));
        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer = $this->defaultRenderer();

        $html_editor_options = array();
        $html_editor_options['width'] = '100%';
        $html_editor_options['height'] = '65';
        $html_editor_options['collapse_toolbar'] = true;
        $html_editor_options['toolbar'] = 'RepositoryQuestion';

        $table_header = array();
        $table_header[] = '<table class="data_table">';
        $table_header[] = '<thead>';
        $table_header[] = '<tr>';
        $table_header[] = '<th class="list"></th>';
        $table_header[] = '<th>' . Translation :: get('HotspotDescription') . '</th>';
        $table_header[] = '<th>' . Translation :: get('Feedback') . '</th>';
        $table_header[] = '<th class="numeric">' . Translation :: get('Score') . '</th>';
        $table_header[] = '<th></th>';
        $table_header[] = '</tr>';
        $table_header[] = '</thead>';
        $table_header[] = '<tbody>';
        $this->addElement('html', implode("\n", $table_header));

        $colours = $this->colours;

        for($option_number = 0; $option_number < $number_of_options; $option_number ++)
        {
            if (! in_array($option_number, $_SESSION['mc_skip_options']))
            {
                $group = array();
                $group[] = $this->createElement('static', null, null, '<div class="colour_box" style="background-color: ' . $colours[$option_number] . ';"></div>');
                //$group[] = $this->createElement('hidden', 'type[' . $option_number . ']', '');
                $group[] = $this->createElement('hidden', 'coordinates[' . $option_number . ']', '');
                $group[] = $this->create_html_editor('answer[' . $option_number . ']', Translation :: get('Answer'), $html_editor_options);
                $group[] = $this->create_html_editor('comment[' . $option_number . ']', Translation :: get('Comment'), $html_editor_options);
                $group[] = $this->createElement('text', 'option_weight[' . $option_number . ']', Translation :: get('Weight'), 'size="2"  class="input_numeric"');

                $hotspot_actions = array();
                $hotspot_actions[] = $this->createElement('image', 'edit[' . $option_number . ']', Theme :: get_common_image_path() . 'action_edit.png', array('class' => 'edit_option', 'id' => 'edit_' . $option_number));
                $hotspot_actions[] = $this->createElement('image', 'reset[' . $option_number . ']', Theme :: get_common_image_path() . 'action_reset.png', array('class' => 'reset_option', 'id' => 'reset_' . $option_number));

                if ($number_of_options - count($_SESSION['mc_skip_options']) > 1)
                {
                    $hotspot_actions[] = $this->createElement('image', 'remove[' . $option_number . ']', Theme :: get_common_image_path() . 'action_delete.png', array('class' => 'remove_option', 'id' => 'remove_' . $option_number));
                }
                else
                {
                    $hotspot_actions[] = $this->createElement('static', null, null, '<img class="remove_option" src="' . Theme :: get_common_image_path() . 'action_delete_na.png" />');
                }
                $group[] = $this->createElement('static', null, null, $this->createElement('group', null, null, $hotspot_actions, '&nbsp;&nbsp;', false)->toHtml());

                $this->addGroup($group, 'option_' . $option_number, null, '', false);

                $renderer->setElementTemplate('<tr id="option_' . $option_number . '" class="' . ($option_number % 2 == 0 ? 'row_even' : 'row_odd') . '">{element}</tr>', 'option_' . $option_number);
                $renderer->setGroupElementTemplate('<td>{element}</td>', 'option_' . $option_number);

            }
        }

        $this->setDefaults();

        $_SESSION['mc_num_options'] = $number_of_options;
        $table_footer[] = '</tbody>';
        $table_footer[] = '</table>';
        $this->addElement('html', implode("\n", $table_footer));

        $this->addGroup($buttons, 'question_buttons', null, '', false);

        $renderer->setElementTemplate('<div style="margin: 10px 0px 10px 0px;">{element}<div class="clear"></div></div>', 'question_buttons');
        $renderer->setGroupElementTemplate('<div style="float:left; text-align: center; margin-right: 10px;">{element}</div>', 'question_buttons');
    }
}
?>
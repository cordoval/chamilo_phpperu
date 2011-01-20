<?php
namespace application\gradebook;

use common\libraries\ResourceManager;
use common\libraries\PlatformSetting;
use common\libraries\Request;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\WebApplication;
use common\libraries\Translation;
use common\libraries\FormValidator;
use common\libraries\EqualityCondition;

use application\weblcms\CourseUserRelation;
use application\weblcms\WeblcmsManager;

use user\UserDataManager;

use ValidateScoreStepRule;
use ValidateScoreBoundariesRule;

require_once dirname(__FILE__) . '/rules/validate_score_boundaries_rule.class.php';
require_once dirname(__FILE__) . '/rules/validate_score_step_rule.class.php';
require_once WebApplication :: get_application_class_lib_path('weblcms') . 'course/course_user_relation.class.php';
require_once WebApplication :: get_application_class_lib_path('weblcms') . 'weblcms_manager/weblcms_manager.class.php';
require_once WebApplication :: get_application_class_lib_path('gradebook') . 'data_provider/gradebook_tree_menu_data_provider.class.php';

class CreateExternalItemForm extends FormValidator
{
    const TYPE_CREATE = 1;
    const TYPE_EDIT = 2;

    private $form_type;
    private $evaluation_format;
    private $course_id;
    private $user;
    private $users;

    function __construct($form_type, $action, $category, $user)
    {
        parent :: __construct('external_item_publication_settings', 'post', $action);
        $this->form_type = $form_type;
        $this->user = $user;
        $this->course_id = preg_replace("/[^0-9]/", '', $category);
        if ($this->form_type == self :: TYPE_CREATE)
        {
            $this->build_basic_creation_form();
        }
        else
        {

            $this->build_evaluation_format_element();
            $this->build_editing_form();
        }
        $this->setDefaults();
    }

    function set_allow_creation($value)
    {
        $this->allow_creation = $value;
    }

    function is_creation_allowed()
    {
        return $this->allow_creation;
    }

    function build_basic_form($users_ids = null)
    {
        $counter = 0;
        $this->addElement('category', Translation :: get('ScoreProperties'));
        $values = $this->getSubmitValues();
        $format = EvaluationManager :: retrieve_evaluation_format($values['format_id']);
        if (! $format)
            $format = EvaluationManager :: retrieve_evaluation_format($this->evaluation->get_format_id());
        $this->evaluation_format = EvaluationFormat :: factory($format->get_title());
        $condition = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $this->course_id);
        $users_relations = WeblcmsManager :: retrieve_course_user_relations($condition);
        $udm = UserDataManager :: get_instance();
        if ($this->evaluation_format->get_score_information())
        {
            $this->addElement('static', null, null, '<em>' . $this->evaluation_format->get_score_information() . '</em>');
        }
        $this->addElement('static', null, null, '<em>Score - Comment</em>');
        if ($users_ids)
        {
            $this->users = $users_ids;
        }
        else
        {
            while ($user = $users_relations->next_result())
            {
                $this->users[] = $user->get_user();
            }
        }
        foreach ($this->users as $user)
        {
            $username = $udm->retrieve_user($user)->get_fullname();
            $group = array();
            if (! $this->evaluation_format->get_score_set())
            {
                $score_rule = new ValidateScoreStepRule($this->evaluation_format);
                $boundaries_rule = new ValidateScoreBoundariesRule($this->evaluation_format);

                $rule = array();
                $group[] = $this->createElement($this->evaluation_format->get_evaluation_field_type(), $this->evaluation_format->get_evaluation_field_name() . $counter, false, array(
                        'size' => '4'));
                $group[] = $this->createElement('text', GradeEvaluation :: PROPERTY_COMMENT . $counter, Translation :: get('Comment'), false);
                $this->addGroup($group, 'grades', $username, null, false);
                $rule[$this->evaluation_format->get_evaluation_field_name() . $counter][] = array(
                        Translation :: get('ValueShouldBeNumeric', null, Utilities :: COMMON_LIBRARIES),
                        'numeric');
                $rule[$this->evaluation_format->get_evaluation_field_name() . $counter][] = array(
                        Translation :: get('DecimalValueNotAllowed', null, Utilities :: COMMON_LIBRARIES),
                        $score_rule);
                $rule[$this->evaluation_format->get_evaluation_field_name() . $counter][] = array(
                        Translation :: get('ScoreIsOutsideBoundaries', null, Utilities :: COMMON_LIBRARIES),
                        $boundaries_rule);
                $this->addGroupRule('grades', $rule);
                $counter ++;
            }
            else
            {

                $score_set = $this->evaluation_format->get_score_set();
                $score_set['no_evaluation'] = Translation :: get('NoEvaluation');
                $group[] = $this->createElement($this->evaluation_format->get_evaluation_field_type(), $this->evaluation_format->get_evaluation_field_name() . $counter, null, $score_set);
                $group[] = $this->createElement('text', GradeEvaluation :: PROPERTY_COMMENT . $counter, Translation :: get('Comment'), false);
                $this->addGroup($group, 'grades', $username, null, false);
                $counter ++;
            }
        }
        $this->addElement('category');
    }

    function build_evaluation_format_element()
    {
        $this->addElement('category', Translation :: get('ExternalItemProperties'));
        $this->addElement('html', '<div id="message"></div>');
        $this->add_textfield(ExternalItem :: PROPERTY_TITLE, Translation :: get('Title', null, Utilities :: COMMON_LIBRARIES), true, array(
                'size' => '100',
                'id' => 'title',
                'style' => 'width: 95%'));
        $this->add_html_editor(ExternalItem :: PROPERTY_DESCRIPTION, Translation :: get('Description', null, Utilities :: COMMON_LIBRARIES), $required);
        if ($this->form_type == self :: TYPE_CREATE)
            $formats_array[0] = Translation :: get('ChooseEvaluationFormat');
        $formats = EvaluationManager :: retrieve_all_active_evaluation_formats();
        while ($format = $formats->next_result())
        {
            $formats_array[$format->get_id()] = ucfirst($format->get_title());
        }
        if (! Request :: get(GradebookTreeMenuDataProvider :: PARAM_ID))
        {
            $attributes = array();
            $attributes['search_url'] = Path :: get(WEB_PATH) . 'common/libraries/php/xml_feeds/xml_user_group_feed.php';
            $locale = array();
            $locale['Display'] = Translation :: get('SelectRecipients');
            $locale['Searching'] = Translation :: get('Searching', null, Utilities :: COMMON_LIBRARIES);
            $locale['NoResults'] = Translation :: get('NoResults', null, Utilities :: COMMON_LIBRARIES);
            $locale['Error'] = Translation :: get('Error', null, Utilities :: COMMON_LIBRARIES);
            $attributes['locale'] = $locale;
            $attributes['exclude'] = array('user_' . $this->user->get_id());
            $attributes['defaults'] = array();
            $this->add_receivers('target', Translation :: get('SelectUsers'), $attributes);
            $this->addElement('style_button', 'refresh', Translation :: get('RefreshScoreProperties'));
        }

        if (Request :: get(EvaluationManager :: PARAM_EVALUATION_ACTION) == EvaluationManager :: ACTION_UPDATE)
        {
            if (PlatformSetting :: get_instance()->get('allow_change_format_on_update', 'gradebook'))
            {
                $select = $this->add_select(Evaluation :: PROPERTY_FORMAT_ID, Translation :: get('EvaluationFormat'), $formats_array, false, array(
                        'class' => 'change_evaluation_format'));
                $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/change_evaluation_format.js'));
                $this->addElement('style_submit_button', 'select_format', Translation :: get('Formatter'), array(
                        'class' => 'normal filter'));
            }
        }
        else
        {
            $select = $this->add_select(Evaluation :: PROPERTY_FORMAT_ID, Translation :: get('EvaluationFormat'), $formats_array, false, array(
                    'class' => 'change_evaluation_format'));
            $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/change_evaluation_format.js'));
            $this->addElement('style_submit_button', 'select_format', Translation :: get('Formatter'), array(
                    'class' => 'normal filter'));
        }
        $this->addElement('category');
    }

    function build_basic_creation_form()
    {
        $this->build_evaluation_format_element();
        $values = $this->getSubmitValues();

        if ($values['format_id'] > 0)
        {
            foreach (unserialize($values['target_elements_active_hidden']) as $users_array)
            {
                $users_ids[] = preg_replace("/[^0-9]/", '', $users_array['id']);
            }
            $this->build_creation_form($users_ids);
        }
    }

    function build_editing_form($users_ids = null)
    {
        $this->build_basic_form($users_ids);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Update', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive update'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function build_creation_form($users_ids = null)
    {
        $this->build_basic_form($users_ids);

        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Create', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'positive'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }

    function create_evaluation()
    {
        $export_values = $this->exportValues();
        $external_item = new ExternalItem();
        $external_item->set_title($export_values['title']);
        $external_item->set_description($export_values['description']);
        if (! $this->course_id)
            $external_item->set_category(null);
        else
            $external_item->set_category('C' . $this->course_id);
        if (! $external_item->create())
        {
            return false;
        }
        for($i = 0; $i < (count($export_values) - 6) / 2; $i ++)
        {
            if (! $export_values[$this->evaluation_format->get_evaluation_field_name() . $i] == 'no_evaluation' || ! $export_values[$this->evaluation_format->get_evaluation_field_name() . $i] == null)
            {
                $evaluation = new Evaluation();
                $evaluation->set_evaluator_id($this->user->get_id());
                $evaluation->set_user_id($this->users[$i]);
                $evaluation->set_evaluation_date(time());
                $evaluation->set_format_id($export_values['format_id']);
                if (! $evaluation->create())
                {
                    return false;
                }

                $external_item_instance = new ExternalItemInstance();
                $external_item_instance->set_external_item_id($external_item->get_id());
                $external_item_instance->set_evaluation_id($evaluation->get_id());
                if (! $external_item_instance->create())
                {
                    return false;
                }

                $grade_evaluation = new GradeEvaluation();
                $grade_evaluation->set_score($export_values[$this->evaluation_format->get_evaluation_field_name() . $i]);
                $grade_evaluation->set_comment($export_values['comment' . $i]);
                $grade_evaluation->set_id($evaluation->get_id());
                if (! $grade_evaluation->create(false))
                {
                    return false;
                }
            }
        }
        return true;
    }

    function update_evaluation($evaluation_id)
    {
        $values = $this->exportValues();
        $evaluation = $this->evaluation;
        $evaluation->set_id($evaluation_id);
        $evaluation->set_evaluator_id($this->user->get_id());
        $evaluation->set_user_id($this->publisher_id);
        $evaluation->set_evaluation_date(time());
        if (PlatformSetting :: get_instance()->get('allow_change_format_on_update', 'gradebook'))
        {
            $evaluation->set_format_id($values['format_id']);
        }
        if (! $evaluation->update())
        {
            return false;
        }

        $internal_item_instance = new InternalItemInstance();
        $internal_item_instance->set_internal_item_id(GradebookDataManager :: get_instance()->retrieve_internal_item_by_publication(Request :: get('application'), $this->publication_id)->get_id());
        $internal_item_instance->set_evaluation_id($evaluation_id);
        if (! $internal_item_instance->update())
        {
            return false;
        }

        $grade_evaluation = $this->grade_evaluation;
        $grade_evaluation->set_score($values[$this->evaluation_format->get_evaluation_field_name()]);
        $grade_evaluation->set_comment($values['comment']);
        $grade_evaluation->set_id($evaluation->get_id());
        if (! $grade_evaluation->update())
        {
            return false;
        }

        return true;
    }

    function validate()
    {
        $values = $this->getSubmitValues();
        if ($values['submit'])
        {
            $this->setDefaults();
            return parent :: validate();
        }
    }

}

?>
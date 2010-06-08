<?php
/**
 * $Id: survey_publication_form.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.assessment.component.assessment_survey_publisher
 */
class SurveyPublicationForm extends FormValidator
{

    function SurveyPublicationForm($parent, $survey, $url = '')
    {
        parent :: __construct('assessment', 'post', $url);
        
        $this->addElement('html', '<h4>' . $survey->get_title() . '</h4>');
        $course = $parent->get_course();
        
        $relation_conditions = array();
        $relation_conditions[] = new EqualityCondition(CourseUserRelation :: PROPERTY_COURSE, $course->get_id());
        $relation_condition = new AndCondition($relation_conditions);
        
        $user_relations = WeblcmsDataManager :: get_instance()->retrieve_course_user_relations($relation_condition);
        while ($user_relation = $user_relations->next_result())
        {
            $user = $user_relation->get_user_object();
            $course_users[$user->get_id()] = $user->get_fullname();
        }
        
        $this->addElement('text', 'email_header', Translation :: get('EmailTitle'), array('size' => 80));
        $this->addRule('email_header', Translation :: get('ThisFieldIsRequired'), 'required');
        $this->add_html_editor('email_content', Translation :: get('EmailContent'), true);
        
        $this->addElement('advmultiselect', 'course_users', Translation :: get('SelectUsers'), $course_users, 'style="width: 250px;"');
        $this->addElement('textarea', 'additional_users', Translation :: get('AdditionalUsers'), array('cols' => 50, 'rows' => 2));
        
        $this->addElement('html', '<br />' . Translation :: get('PublishSurveySendMailInfo') . '<br /><br />');
        $this->addElement('checkbox', 'resend', Translation :: get('ResendEmail'));
        $this->addElement('html', '<br />' . Translation :: get('PublishSurveyResendMailInfo') . '<br /><br />');
        //$this->addElement('submit', 'submit', Translation :: get('SendMail'));
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        
        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);
    }
}
?>
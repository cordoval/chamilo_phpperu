<?php
namespace application\weblcms\tool\survey;

use application\weblcms\Tool;
use group\GroupDataManager;
use repository\RepositoryDataManager;
use common\libraries\Toolbar;
use common\libraries\ToolbarItem;
use user\UserDataManager;
use common\libraries\FormValidator;
use common\libraries\PlatformSetting;
use common\libraries\WebApplication;
use common\libraries\Theme;
use common\libraries\Utilities;
use common\libraries\AndCondition;
use common\libraries\EqualityCondition;
use common\libraries\Request;
use common\libraries\Path;
use common\libraries\Translation;
use common\extensions\repo_viewer\RepoViewer;

/**
 * $Id: content_object_publication_form.class.php 218 2009-11-13 14:21:26Z kariboe $
 * @package application.lib.weblcms
 */
require_once dirname(__FILE__) . '/../../content_object_publication.class.php';
require_once dirname(__FILE__) . '/../../content_object_repo_viewer.class.php';
require_once Path :: get_plugin_path() . 'html2text/class.html2text.inc';
require_once Path :: get_application_path() . 'lib/weblcms/trackers/weblcms_survey_participant_tracker.class.php';

/**
 * This class represents a form to allow a user to publish a learning object.
 *
 * The form allows the user to set some properties of the publication
 * (publication dates, target users, visibility, ...)
 */
class SurveyContentObjectPublicationForm extends FormValidator
{
    /**#@+
     * Constant defining a form parameter
     */
    const TYPE_SINGLE = 1;
    const TYPE_MULTI = 2;

    // XXX: Some of these constants heavily depend on FormValidator.
    const PARAM_CATEGORY_ID = 'category';
    const PARAM_TARGET = 'target_users_and_course_groups';
    const PARAM_TARGET_ELEMENTS = 'target_users_and_course_groups_elements';
    const PARAM_TARGET_OPTION = 'target_users_and_course_groups_option';
    const PARAM_FOREVER = 'forever';
    const PARAM_FROM_DATE = 'from_date';
    const PARAM_TO_DATE = 'to_date';
    const PARAM_HIDDEN = 'hidden';
    const PARAM_EMAIL = 'email';
    /**#@-*/
    /**
     * The tool in which the publication will be made
     */
    private $tool;
    /**
     * The learning object that will be published
     */
    private $content_object;
    /**
     * The publication that will be changed (when using this form to edit a
     * publication)
     */
    private $publication;
    /**
     * Is a 'send by email' option available?
     */
    private $email_option;
    /**
     * The course we're publishing in
     */
    private $course;

    private $user;

    private $repo_viewer;

    private $form_type;

    /**
     * Creates a new learning object publication form.
     * @param ContentObject The learning object that will be published
     * @param string $tool The tool in which the object will be published
     * @param boolean $email_option Add option in form to send the learning
     * object by email to the receivers
     */
    function SurveyContentObjectPublicationForm($form_type, $content_object, $repo_viewer, $email_option = false, $course, $in_repo_viewer = true, $extra_parameters = array())
    {
        if ($repo_viewer)
        {
            $pub_param = $repo_viewer->get_parameters();
        }

        $this->form_type = $form_type;
        switch ($this->form_type)
        {
            case self :: TYPE_SINGLE :
                if (get_class($content_object) == 'Introduction')
                {
                    $parameters = array_merge($pub_param, array(ContentObjectRepoViewer :: PARAM_ID => $content_object->get_id(), Tool :: PARAM_ACTION => $in_repo_viewer ? Tool :: ACTION_PUBLISH_INTRODUCTION : null));
                }
                else
                {
                    $parameters = array_merge($pub_param, array(ContentObjectRepoViewer :: PARAM_ID => $content_object->get_id(), Tool :: PARAM_ACTION => $in_repo_viewer ? Tool :: ACTION_PUBLISH : null));
                }
                break;
            case self :: TYPE_MULTI :
                $parameters = array_merge($pub_param, array(Tool :: PARAM_ACTION => $in_repo_viewer ? Tool :: ACTION_PUBLISH : null, ContentObjectRepoViewer :: PARAM_ID => $content_object));
                break;
        }

        $parameters = array_merge($parameters, $extra_parameters);

        $url = $repo_viewer->get_url($parameters);
        parent :: __construct('publish', 'post', $url);

        $this->repo_viewer = $repo_viewer;

        if ($in_repo_viewer)
        {
            $this->tool = $repo_viewer->get_parent()->get_parent();
        }
        else
        {
            $this->tool = $repo_viewer->get_parent();
        }
        $this->content_object = $content_object;
        $this->email_option = $email_option;
        $this->course = $course;
        $this->user = $repo_viewer->get_user();

        switch ($this->form_type)
        {
            case self :: TYPE_SINGLE :
                $this->build_single_form();
                break;
            case self :: TYPE_MULTI :
                $this->build_multi_form();
                break;
        }
        $this->add_footer();
        $this->setDefaults();
    }

    /**
     * Sets the publication. Use this function if you're using this form to
     * change the settings of a learning object publication.
     * @param ContentObjectPublication $publication
     */
    function set_publication($publication)
    {
        $this->publication = $publication;
        $this->addElement('hidden', Tool :: PARAM_PUBLICATION_ID);
        $this->addElement('hidden', 'action');
        $defaults['action'] = 'edit';
        $defaults[Tool :: PARAM_PUBLICATION_ID] = $publication->get_id();
        $defaults[ContentObjectPublication :: PROPERTY_FROM_DATE] = $publication->get_from_date();
        $defaults[ContentObjectPublication :: PROPERTY_TO_DATE] = $publication->get_to_date();
        if ($defaults[ContentObjectPublication :: PROPERTY_FROM_DATE] != 0)
        {
            $defaults[self :: PARAM_FOREVER] = 0;
        }
        $defaults[ContentObjectPublication :: PROPERTY_HIDDEN] = $publication->is_hidden();
        $defaults[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] = $publication->get_show_on_homepage();

        $udm = UserDataManager :: get_instance();
        $wdm = WeblcmsDataManager :: get_instance();
        $gdm = GroupDataManager :: get_instance();

        $target_course_groups = $this->publication->get_target_course_groups();
        $target_users = $this->publication->get_target_users();
        $target_groups = $this->publication->get_target_groups();

        $defaults[self :: PARAM_TARGET_ELEMENTS] = array();
        foreach ($target_course_groups as $target_course_group)
        {
            $group = $wdm->retrieve_course_group($target_course_group);

            $selected_group = array();
            $selected_group['id'] = 'group_' . $group->get_id();
            $selected_group['classes'] = 'type type_group';
            $selected_group['title'] = $group->get_name();
            $selected_group['description'] = $group->get_name();

            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_group['id']] = $selected_group;
        }

        foreach ($target_users as $target_user)
        {
            $user = $udm->retrieve_user($target_user);

            $selected_user = array();
            $selected_user['id'] = 'user_' . $user->get_id();
            $selected_user['classes'] = 'type type_user';
            $selected_user['title'] = $user->get_fullname();
            $selected_user['description'] = $user->get_username();

            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_user['id']] = $selected_user;
        }

        foreach ($target_groups as $target_group)
        {
            $group = $gdm->retrieve_group($target_group);

            $selected_group = array();
            $selected_group['id'] = 'platform_' . $group->get_id();
            $selected_group['classes'] = 'type type_group';
            $selected_group['title'] = $group->get_name();
            $selected_group['description'] = $group->get_name();

            $defaults[self :: PARAM_TARGET_ELEMENTS][$selected_group['id']] = $selected_group;
        }

        if (count($defaults[self :: PARAM_TARGET_ELEMENTS]) > 0)
        {
            $defaults[self :: PARAM_TARGET_OPTION] = '1';
        }

        $active = $this->getElement(self :: PARAM_TARGET_ELEMENTS);
        $active->_elements[0]->setValue(serialize($defaults[self :: PARAM_TARGET_ELEMENTS]));

        parent :: setDefaults($defaults);
    }

    /**
     * Sets the default values of the form.
     *
     * By default the publication is for everybody who has access to the tool
     * and the publication will be available forever.
     */
    function setDefaults()
    {
        $defaults = array();
        $defaults[self :: PARAM_TARGET_OPTION] = 0;
        $defaults[self :: PARAM_FOREVER] = 1;
        $defaults[self :: PARAM_CATEGORY_ID] = Request :: get('pcattree');
        parent :: setDefaults($defaults);
    }

    function build_single_form()
    {
        $this->build_form();
    }

    function build_multi_form()
    {
        $this->build_form();
        $this->addElement('hidden', 'ids', serialize($this->content_object));
    }

    private $categories;
    private $level = 1;

    function get_categories($parent_id)
    {
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, Request :: get('course'));
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, Request :: get('tool'));
        $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_PARENT, $parent_id);
        $condition = new AndCondition($conditions);

        $cats = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication_categories($condition);
        while ($cat = $cats->next_result())
        {
            $this->categories[$cat->get_id()] = str_repeat('--', $this->level) . ' ' . $cat->get_name();
            $this->level ++;
            $this->get_categories($cat->get_id());
            $this->level --;
        }
    }

    /**
     * Builds the form by adding the necessary form elements.
     */
    function build_form()
    {
        if (WebApplication :: is_active('gradebook'))
        {
            if (PlatformSetting :: get_instance()->get('allow_evaluate_' . Request :: get(WeblcmsManager :: PARAM_TOOL), 'gradebook'))
            {
                require_once dirname(__FILE__) . '/../gradebook/forms/gradebook_internal_item_form.class.php';
                $gradebook_internal_item_form = new GradebookInternalItemForm();
                $gradebook_internal_item_form->build_evaluation_question($this);
            }
        }
        $this->categories[0] = Translation :: get('Root');
        $this->get_categories(0);

        //$categories = $this->repo_viewer->get_categories(true);
        if (count($this->categories) > 1)
        {
            // More than one category -> let user select one
            $this->addElement('select', self :: PARAM_CATEGORY_ID, Translation :: get('Category'), $this->categories);
        }
        else
        {
            // Only root category -> store object in root category
            $this->addElement('hidden', ContentObjectPublication :: PROPERTY_CATEGORY_ID, 0);
        }

        $attributes = array();
        $attributes['search_url'] = Path :: get(WEB_PATH) . 'application/weblcms/php/xml_feeds/xml_course_user_group_feed.php?course=' . $this->course->get_id();
        $locale = array();
        $locale['Display'] = Translation :: get('SelectRecipients');
        $locale['Searching'] = Translation :: get('Searching');
        $locale['NoResults'] = Translation :: get('NoResults');
        $locale['Error'] = Translation :: get('Error');
        $attributes['locale'] = $locale;
        //$attributes['exclude'] = array('user_' . $this->tool->get_user_id());
        $attributes['defaults'] = array();

        $legend_items = array();
        $legend_items[] = new ToolbarItem(Translation :: get('CourseUser'), Theme :: get_common_image_path() . 'treemenu/user.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');
        $legend_items[] = new ToolbarItem(Translation :: get('LinkedUser'), Theme :: get_common_image_path() . 'treemenu/user_platform.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');
        $legend_items[] = new ToolbarItem(Translation :: get('UserGroup'), Theme :: get_common_image_path() . 'treemenu/group.png', null, ToolbarItem :: DISPLAY_ICON_AND_LABEL, false, 'legend');

        $legend = new Toolbar();
        $legend->set_items($legend_items);
        $legend->set_type(Toolbar :: TYPE_HORIZONTAL);

        $this->add_receivers(self :: PARAM_TARGET, Translation :: get('PublishFor'), $attributes, 'Everybody', $legend);

        $this->add_forever_or_timewindow();
        $this->addElement('checkbox', self :: PARAM_HIDDEN, Translation :: get('Hidden'));
        if ($this->email_option)
        {
            $this->addElement('checkbox', self :: PARAM_EMAIL, Translation :: get('SendByEMail'));
        }
        $this->addElement('checkbox', ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE, Translation :: get('ShowOnHomepage'));
    }

    function add_footer()
    {
        $buttons[] = $this->createElement('style_submit_button', 'submit', Translation :: get('Publish'), array('class' => 'positive publish'));
        $buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));

        $this->addGroup($buttons, 'buttons', null, '&nbsp;', false);

     //$this->addElement('submit', 'submit', Translation :: get('Ok'));
    }

    /**
     * Updates a learning object publication using the values from the form.
     * @return ContentObjectPublication The updated publication
     * @todo This function shares some code with function
     * create_content_object_publication. This code duplication should be
     * resolved.
     */
    function update_content_object_publication()
    {
        $values = $this->exportValues();
        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }
        $hidden = ($values[self :: PARAM_HIDDEN] ? 1 : 0);
        $category = $values[self :: PARAM_CATEGORY_ID];

        $users = $values[self :: PARAM_TARGET_ELEMENTS]['user'];
        $course_groups = $values[self :: PARAM_TARGET_ELEMENTS]['group'];
        $groups = $values[self :: PARAM_TARGET_ELEMENTS]['platform'];

        $pub = $this->publication;
        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_hidden($hidden);
        $modifiedDate = time();
        $pub->set_modified_date($modifiedDate);
        $pub->set_target_users($users);
        $pub->set_target_course_groups($course_groups);
        $pub->set_target_groups($groups);
        $show_on_homepage = ($values[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);
        $pub->set_show_on_homepage($show_on_homepage);
        $pub->set_category_id($category);
        $pub->update();
        return $pub;
    }

    /**
     * Creates a learning object publication using the values from the form.
     * @return ContentObjectPublication The new publication
     */
    function create_content_object_publication()
    {
        // TODO: Seems like the modified date isn't being written to the DB
        // TODO: Hidden is not being used correctly
        $values = $this->exportValues();

        if ($values[self :: PARAM_FOREVER] != 0)
        {
            $from = $to = 0;
        }
        else
        {
            $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
            $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
        }

        $course = $this->course->get_id();
        $tool = $this->repo_viewer->get_tool()->get_tool_id();
        $tool = (is_null($tool) ? 'introduction' : $tool);
        $category = $values[self :: PARAM_CATEGORY_ID];

        $wdm = WeblcmsDataManager :: get_instance();
        $index = $wdm->get_next_content_object_publication_display_order_index($course, $tool, $category);

        $pub = new ContentObjectPublication();
        $pub->set_content_object_id($this->content_object->get_id());
        $pub->set_course_id($course);
        $pub->set_tool($tool);
        $pub->set_category_id($category);

        if ($values[self :: PARAM_TARGET_OPTION])
        {
            $pub->set_target_users($values[self :: PARAM_TARGET_ELEMENTS]['user']);
            $pub->set_target_course_groups($values[self :: PARAM_TARGET_ELEMENTS]['group']);
            $pub->set_target_groups($values[self :: PARAM_TARGET_ELEMENTS]['platform']);
        }

        $pub->set_from_date($from);
        $pub->set_to_date($to);
        $pub->set_publisher_id($this->user->get_id());
        $pub->set_publication_date(time());
        $pub->set_modified_date(time());
        $pub->set_hidden($values[self :: PARAM_HIDDEN] ? 1 : 0);
        $pub->set_display_order_index($index);
        $pub->set_email_sent(false);
        $pub->set_show_on_homepage($values[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);

        if (! $pub->create())
        {
            return false;
        }
        else
        {
            $target_users = $pub->get_target_users();
            foreach ($target_users as $target_user)
            {
                $this->create_participant_trackers($target_user, $id, $pub->get_id());
            }
        }
        return $pub;
    }

    function create_content_object_publications()
    {
        $values = $this->exportValues();

        $ids = unserialize($values['ids']);

        foreach ($ids as $id)
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($id);

            if ($values[self :: PARAM_FOREVER] != 0)
            {
                $from = $to = 0;
            }
            else
            {
                $from = Utilities :: time_from_datepicker($values[self :: PARAM_FROM_DATE]);
                $to = Utilities :: time_from_datepicker($values[self :: PARAM_TO_DATE]);
            }

            $course = $this->course->get_id();
            $tool = $this->repo_viewer->get_tool()->get_tool_id();
            $tool = (is_null($tool) ? 'introduction' : $tool);
            $category = $values[self :: PARAM_CATEGORY_ID];
            if (! $category)
                $category = 0;

            $wdm = WeblcmsDataManager :: get_instance();
            $index = $wdm->get_next_content_object_publication_display_order_index($course, $tool, $category);

            $pub = new ContentObjectPublication();
            $pub->set_content_object_id($id);
            $pub->set_course_id($course);
            $pub->set_tool($tool);
            $pub->set_category_id($category);

            if ($values[self :: PARAM_TARGET_OPTION])
            {
                $pub->set_target_users($values[self :: PARAM_TARGET_ELEMENTS]['user']);
                $pub->set_target_course_groups($values[self :: PARAM_TARGET_ELEMENTS]['group']);
                $pub->set_target_groups($values[self :: PARAM_TARGET_ELEMENTS]['platform']);
            }

            $pub->set_from_date($from);
            $pub->set_to_date($to);
            $pub->set_publisher_id($this->user->get_id());
            $pub->set_publication_date(time());
            $pub->set_modified_date(time());
            $pub->set_hidden($values[self :: PARAM_HIDDEN] ? 1 : 0);
            $pub->set_display_order_index($index);
            $pub->set_email_sent(false);
            $pub->set_show_on_homepage($values[ContentObjectPublication :: PROPERTY_SHOW_ON_HOMEPAGE] ? 1 : 0);

            if (! $pub->create())
            {
                return false;
            }
            else
            {
                $target_users = $pub->get_target_users();
                foreach ($target_users as $target_user)
                {
                    $this->create_participant_trackers($target_user, $id, $pub->get_id());
                }
            }
        }
        return true;
    }

    private function create_participant_trackers($user_id, $id, $publication_id)
    {
        $dm = UserDataManager :: get_instance();
        $user_name = $dm->retrieve_user($user_id)->get_email();
        $survey = RepositoryDataManager :: get_instance()->retrieve_content_object($id);

        $template = $survey->get_context_template();
        $this->create_contexts($user_id, $template, $user_name, $publication_id);
    }

    private function create_contexts($user_id, $template, $key, $publication_id, $parent_participant_id = 0)
    {
        $context_type = $template->get_context_type();
        $key_type = $template->get_key_type();

        $context = SurveyContext :: factory($context_type);
        $contexts = $context->create_contexts_for_user($user_id, $key, $key_type);

        $args = array();
        $args[WeblcmsSurveyParticipantTracker :: PROPERTY_SURVEY_PUBLICATION_ID] = $publication_id;
        $args[WeblcmsSurveyParticipantTracker :: PROPERTY_USER_ID] = $user_id;
        $args[WeblcmsSurveyParticipantTracker :: PROPERTY_PARENT_ID] = $parent_participant_id;
        $args[WeblcmsSurveyParticipantTracker :: PROPERTY_CONTEXT_TEMPLATE_ID] = $template->get_id();

        foreach ($contexts as $cont)
        {
            $args[WeblcmsSurveyParticipantTracker :: PROPERTY_CONTEXT_ID] = $cont->get_id();
            $args[WeblcmsSurveyParticipantTracker :: PROPERTY_CONTEXT_NAME] = $cont->get_name();
            $tracker = Event :: trigger('weblcms_survey_participation', 'weblcms', $args);
            if ($template->has_children())
            {
                $temps = $template->get_children(false);
                while ($temp = $temps->next_result())
                {
                    $key = $cont->get_additional_property($temp->get_key_type());
                    $this->create_contexts($user_id, $temp, $key, $tracker[0]->get_id());
                }
            }
        }
    }
}
?>

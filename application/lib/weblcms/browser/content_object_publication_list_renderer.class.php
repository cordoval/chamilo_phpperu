<?php
/**
 * $Id: content_object_publication_list_renderer.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.browser
 */
/**
 * This is a generic renderer for a set of learning object publications.
 * @package application.weblcms.tool
 * @author Bart Mollet
 * @author Tim De Pauw
 */
abstract class ContentObjectPublicationListRenderer
{
    const TYPE_LIST = 'list';
    const TYPE_TABLE = 'table';
    const TYPE_GALLERY = 'gallery_table';
    const TYPE_SLIDESHOW = 'slideshow';
    const TYPE_CALENDAR = 'calendar';
    const TYPE_MONTH = 'month_calendar';
    const TYPE_MINI_MONTH = 'mini_month_calendar';
    const TYPE_WEEK = 'week_calendar';
    const TYPE_DAY = 'day_calendar';

    protected $tool_browser;
    private $parameters;
    private $actions;

    /**
     * Constructor.
     * @param PublicationBrowser $tool_browser The tool_browser to associate this list
     * renderer with.
     * @param array $parameters The parameters to pass to the renderer.
     */
    function ContentObjectPublicationListRenderer($tool_browser, $parameters = array ())
    {
        $this->parameters = $parameters;
        $this->tool_browser = $tool_browser;
    }

    function get_actions()
    {
        return $this->actions;
    }

    function set_actions($actions)
    {
        $this->actions = $actions;
    }

    /**
     * Renders the title of the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_title($publication)
    {
        return htmlspecialchars($publication->get_content_object()->get_title());
    }

    /**
     * Renders the description of the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_description($publication)
    {
        $content_object = $publication->get_content_object();
        $display = ContentObjectDisplay :: factory($content_object);
        return $display->get_description();
    }

    /**
     * Renders information about the repo_viewer of the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_repo_viewer($publication)
    {
        $user = $this->tool_browser->get_parent()->get_user_info($publication->get_publisher_id());
        return $user->get_firstname() . ' ' . $user->get_lastname();
    }

    /**
     * Renders the date when the given publication was published.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_publication_date($publication)
    {
        return $this->format_date($publication->get_publication_date());
    }

    /**
     * Renders the users and course_groups the given publication was published for.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_publication_targets($publication)
    {
        if ($publication->is_email_sent())
        {
            $email_suffix = ' - <img src="' . Theme :: get_common_image_path() . 'action_email.png" alt="" style="vertical-align: middle;"/>';
        }
        if ($publication->is_for_everybody())
        {
            return htmlentities(Translation :: get('Everybody')) . $email_suffix;
        }
        else
        {
            $users = $publication->get_target_users();
            $course_groups = $publication->get_target_course_groups();
            $groups = $publication->get_target_groups();
            if (count($users) + count($course_groups) + count($groups) == 1)
            {
                if (count($users) == 1)
                {
                    $user = $this->tool_browser->get_parent()->get_user_info($users[0]);
                    return $user->get_firstname() . ' ' . $user->get_lastname() . $email_suffix;
                }
                elseif (count($groups) == 1)
                {
                    $gdm = GroupDataManager :: get_instance();
                    $group = $gdm->retrieve_group($groups[0]);
                    return $group->get_name();
                }
                else
                {
                    $wdm = WeblcmsDataManager :: get_instance();
                    $course_group = $wdm->retrieve_course_group($course_groups[0]);
                    return $course_group->get_name();
                }
            }
            $target_list = array();
            $target_list[] = '<select>';
            foreach ($users as $index => $user_id)
            {
                $user = $this->tool_browser->get_parent()->get_user_info($user_id);
                $target_list[] = '<option>' . $user->get_firstname() . ' ' . $user->get_lastname() . '</option>';
            }
            foreach ($course_groups as $index => $course_group_id)
            {
                $wdm = WeblcmsDataManager :: get_instance();
                //Todo: make this more efficient. Get all course_groups using a single query
                $course_group = $wdm->retrieve_course_group($course_group_id);
                $target_list[] = '<option>' . $course_group->get_name() . '</option>';
            }
            foreach ($groups as $index => $group_id)
            {
                $gdm = GroupDataManager :: get_instance();
                //Todo: make this more efficient. Get all course_groups using a single query
                $group = $gdm->retrieve_group($group_id);
                $target_list[] = '<option>' . $group->get_name() . '</option>';
            }
            $target_list[] = '</select>';
            return implode("\n", $target_list) . $email_suffix;
        }
    }

    /**
     * Renders the time period in which the given publication is active.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_publication_period($publication)
    {
        if ($publication->is_forever())
        {
            return htmlentities(Translation :: get('Forever'));
        }
        return htmlentities(Translation :: get('From') . ' ' . $this->format_date($publication->get_from_date()) . ' ' . Translation :: get('Until') . ' ' . $this->format_date($publication->get_to_date()));
    }

    /**
     * Renders general publication information about the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_publication_information($publication)
    {
        $repo_viewer = $this->tool_browser->get_parent()->get_user_info($publication->get_publisher_id());
        $html = array();
        $html[] = htmlentities(Translation :: get('PublishedOn')) . ' ' . $this->render_publication_date($publication);
        $html[] = htmlentities(Translation :: get('By')) . ' ' . $this->render_repo_viewer($publication);
        $html[] = htmlentities(Translation :: get('For')) . ' ' . $this->render_publication_targets($publication);
        if (! $publication->is_forever())
        {
            $html[] = '(' . $this->render_publication_period($publication) . ')';
        }
        return implode("\n", $html);
    }

    /**
     * Renders the means to move the given publication up one place.
     * @param ContentObjectPublication $publication The publication.
     * @param boolean $first True if the publication is the first in the list
     * it is a part of.
     * @return string The HTML rendering.
     */
    function render_up_action($publication, $first = false)
    {
        if (! $first)
        {
            $up_img = 'action_up.png';
            $up_url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_UP, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
            $up_link = '<a href="' . $up_url . '"><img src="' . Theme :: get_common_image_path() . $up_img . '" alt=""/></a>';
        }
        else
        {
            $up_link = '<img src="' . Theme :: get_common_image_path() . 'action_up_na.png"  alt=""/>';
        }
        return $up_link;
    }

    /**
     * Renders the means to move the given publication down one place.
     * @param ContentObjectPublication $publication The publication.
     * @param boolean $last True if the publication is the last in the list
     * it is a part of.
     * @return string The HTML rendering.
     */
    function render_down_action($publication, $last = false)
    {
        if (! $last)
        {
            $down_img = 'action_down.png';
            $down_url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_DOWN, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
            $down_link = '<a href="' . $down_url . '"><img src="' . Theme :: get_common_image_path() . $down_img . '"  alt=""/></a>';
        }
        else
        {
            $down_link = '<img src="' . Theme :: get_common_image_path() . 'action_down_na.png"  alt=""/>';
        }
        return $down_link;
    }

    /**
     * Renders the means to toggle visibility for the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_visibility_action($publication)
    {
        $visibility_url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
        if ($publication->is_hidden())
        {
            $visibility_img = 'action_invisible.png';
        }
        elseif ($publication->is_forever())
        {
            $visibility_img = 'action_visible.png';
        }
        else
        {
            $visibility_img = 'action_period.png';
            $visibility_url = 'javascript:void(0)';
        }
        $visibility_link = '<a href="' . $visibility_url . '"><img src="' . Theme :: get_common_image_path() . $visibility_img . '"  alt=""/></a>';
        return $visibility_link;
    }

    /**
     * Renders the means to edit the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_edit_action($publication)
    {
        $edit_url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_UPDATE, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
        $edit_link = '<a href="' . $edit_url . '"><img src="' . Theme :: get_common_image_path() . 'action_edit.png"  alt=""/></a>';
        return $edit_link;
    }

    function render_top_action($publication)
    {
        return '<a href="#top"><img src="' . Theme :: get_common_image_path() . 'action_ajax_add.png"  alt=""/></a>';
    }

    /**
     * Renders the means to delete the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_delete_action($publication)
    {
        $delete_url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
        $delete_link = '<a href="' . $delete_url . '" onclick="return confirm(\'' . addslashes(htmlentities(Translation :: get('ConfirmYourChoice'))) . '\');"><img src="' . Theme :: get_common_image_path() . 'action_delete.png"  alt=""/></a>';
        return $delete_link;
    }

    /**
     * Renders the means to give feedback to the given publication
     * @param ContentObjectPublication $publication The publication
     *
     */
    function render_feedback_action($publication)
    {
        $feedback_url = $this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => 'view'), array(), true);
        $feedback_link = '<a href="' . $feedback_url . '"><img src="' . Theme :: get_common_image_path() . 'action_browser.png" alt=""/></a>';
        return $feedback_link;
    }

    function render_evaluation_action($publication)
    {
        require_once dirname(__FILE__) . '/../../gradebook/evaluation_manager/evaluation_manager.class.php';
        if (EvaluationManager :: retrieve_internal_item_by_publication(WeblcmsManager :: APPLICATION_NAME, $publication->get_id()))
        {
            $evaluation_url = $this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_EVALUATE_TOOL_PUBLICATION), array(), true);
            $evaluation_link = '<a href="' . $evaluation_url . '"><img src="' . Theme :: get_common_image_path() . 'action_evaluation.png" alt=""/></a>';
            return $evaluation_link;
        }
    }

    /**
     * Renders the means to move the given publication to another category.
     * @param ContentObjectPublication $publication The publication.
     * @return string The HTML rendering.
     */
    function render_move_to_category_action($publication)
    {
        if ($this->get_tool_browser() instanceof Categorizable)
        {

            $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_COURSE, $this->tool_browser->get_parent()->get_course_id());
            $conditions[] = new EqualityCondition(ContentObjectPublicationCategory :: PROPERTY_TOOL, $this->tool_browser->get_parent()->get_tool_id());
            $count = WeblcmsDataManager :: get_instance()->count_content_object_publication_categories(new AndCondition($conditions));
            $count ++;
            if ($count > 1)
            {
                $url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()), array(), true);
                $link = '<a href="' . $url . '"><img src="' . Theme :: get_common_image_path() . 'action_move.png"  alt=""/></a>';
            }
            else
            {
                $link = '<img src="' . Theme :: get_common_image_path() . 'action_move_na.png"  alt=""/>';
            }
            return $link;
        }
        else
        {
            return null;
        }
    }

    /**
     * Renders the attachements of a publication.
     * @param ContentObjectPublication $publication The publication.
     * @return string The rendered HTML.
     */
    /*function render_attachments($publication)
    {
        $object = $publication->get_content_object();
        if ($object instanceof AttachmentSupport)
        {
            $attachments = $object->get_attached_content_objects();
            if(count($attachments)>0)
            {
                $html[] = '<h4>Attachments</h4>';
                Utilities :: order_content_objects_by_title($attachments);
                foreach ($attachments as $attachment)
                {
                    $disp = ContentObjectDisplay :: factory($attachment);
                    $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path().'content_object/'.$attachment->get_icon_name().$icon_suffix.'.png);">';
                    $html[] = '<div class="title">';
                    $html[] = $attachment->get_title();
                    $html[] = '</div>';
                    $html[] = '<div class="description">';
                    $html[] = $attachment->get_description();
                    $html[] = '</div></div>';
                    //$html[] = '<li><img src="'.Theme :: get_common_image_path().'treemenu_types/'.$attachment->get_type().'.png" alt="'.htmlentities(Translation :: get(ContentObject :: type_to_class($attachment->get_type()).'TypeName')).'"/> '.$disp->get_short_html().'</li>';
                }
                //$html[] = '</ul>';
                return implode("\n",$html);
            }
        }
        return '';
    }*/

    function render_attachments($publication)
    {
        $object = $publication->get_content_object();
        if ($object instanceof AttachmentSupport)
        {
            $attachments = $object->get_attached_content_objects();
            if (count($attachments) > 0)
            {
                $html[] = '<h4>Attachments</h4>';
                Utilities :: order_content_objects_by_title($attachments);
                $html[] = '<ul>';
                foreach ($attachments as $attachment)
                {
                    $html[] = '<li><a href="' . $this->tool_browser->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_VIEW_ATTACHMENT, Tool :: PARAM_OBJECT_ID => $attachment->get_id())) . '"><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $attachment->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($attachment->get_type()) . 'TypeName')) . '"/> ' . $attachment->get_title() . '</a></li>';
                }
                $html[] = '</ul>';
                return implode("\n", $html);
            }
        }
        return '';
    }

    /**
     * Renders publication actions for the given publication.
     * @param ContentObjectPublication $publication The publication.
     * @param boolean $first True if the publication is the first in the list
     * it is a part of.
     * @param boolean $last True if the publication is the last in the list
     * it is a part of.
     * @return string The rendered HTML.
     */
    function render_publication_actions($publication, $first, $last)
    {
        //        return $this->get_publication_actions($publication)->as_html();
        $html = array();
        //
        //        if ($this->is_allowed(DELETE_RIGHT))
        //        {
        //            $icons[] = $this->render_delete_action($publication);
        //        }
        //        if ($this->is_allowed(EDIT_RIGHT))
        //        {
        //            $icons[] = $this->render_edit_action($publication);
        //            $icons[] = $this->render_visibility_action($publication);
        //            $icons[] = $this->render_up_action($publication, $first);
        //            $icons[] = $this->render_down_action($publication, $last);
        //            $icons[] = $this->render_move_to_category_action($publication, $last);
        //        }
        //
        //        $icons[] = $this->render_feedback_action($publication);
        //
        //        if (WebApplication :: is_active('gradebook'))
        //            $icons[] = $this->render_evaluation_action($publication);
        //
        //        //dump($icons);
        //        $html[] = implode('&nbsp;', $icons);
        $html[] = $this->get_publication_actions($publication)->as_html();
        return implode($html);
    }

    /**
     * Renders the icon for the given publication
     * @param ContentObjectPublication $publication The publication.
     * @return string The rendered HTML.
     */
    function render_icon($publication)
    {
        $object = $publication->get_content_object();
        return '<img src="' . Theme :: get_common_image_path() . 'content_object/' . $object->get_icon_name() . '.png" alt=""/>';
    }

    /**
     * Formats the given date in a human-readable format.
     * @param int $date A UNIX timestamp.
     * @return string The formatted date.
     */
    function format_date($date)
    {
        $date_format = Translation :: get('dateTimeFormatLong');
        return DatetimeUtilities :: format_locale_date($date_format, $date);
    }

    /**
     * @see ContentObjectPublicationBrowser :: get_publications()
     */
    function get_publications($offset = 0, $max_objects = -1, ObjectTableOrder $object_table_order = null)
    {
        if (!$object_table_order)
        {
            $object_table_order = new ObjectTableOrder(ContentObjectPublication :: PROPERTY_DISPLAY_ORDER_INDEX, SORT_DESC);
        }

        return $this->tool_browser->get_publications($offset, $max_objects, $object_table_order);
    }

    /**
     * @see ContentObjectPublicationBrowser :: get_publication_count()
     */
    function get_publication_count()
    {
        return $this->tool_browser->get_publication_count();
    }

    /**
     * Returns the value of the given renderer parameter.
     * @param string $name The name of the parameter.
     * @return mixed The value of the parameter.
     */
    function get_parameter($name)
    {
        return $this->parameters[$name];
    }

    /**
     * Sets the value of the given renderer parameter.
     * @param string $name The name of the parameter.
     * @param mixed $value The new value for the parameter.
     */
    function set_parameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Returns the output of the list renderer as HTML.
     * @return string The HTML.
     */
    abstract function as_html();

    /**
     * @see ContentObjectPublicationBrowser :: get_url()
     */
    function get_url($parameters = array (), $filter = array(), $encode_entities = false)
    {
        return $this->tool_browser->get_url($parameters, $filter, $encode_entities);
    }

    function get_complex_builder_url($publication_id)
    {
        return $this->tool_browser->get_complex_builder_url($publication_id);
    }
    
	function get_complex_display_url($publication_id)
    {
        return $this->tool_browser->get_complex_builder_url($publication_id);
    }

    /**
     * @see ContentObjectPublicationBrowser :: is_allowed()
     */
    function is_allowed($right)
    {
        return $this->tool_browser->is_allowed($right);
    }

    /**
     *
     */
    protected function object2color($object)
    {
        $color_number = substr(ereg_replace('[0a-zA-Z]', '', md5(serialize($object))), 0, 9);
        $rgb = array();
        $rgb['r'] = substr($color_number, 0, 3) % 255;
        $rgb['g'] = substr($color_number, 2, 3) % 255;
        $rgb['b'] = substr($color_number, 4, 3) % 255;

        $rgb['fr'] = round(($rgb['r'] + 234) / 2);
        $rgb['fg'] = round(($rgb['g'] + 234) / 2);
        $rgb['fb'] = round(($rgb['b'] + 234) / 2);

        return $rgb;
    }

    static function factory($type, $tool_browser)
    {
        $file = dirname(__FILE__) . '/list_renderer/' . $type . '_content_object_publication_list_renderer.class.php';
        if (! file_exists($file))
        {
            throw new Exception(Translation :: get('ContentObjectPublicationListRendererTypeDoesNotExist', array('type' => $type)));
        }

        require_once $file;

        $class = Utilities :: underscores_to_camelcase($type) . 'ContentObjectPublicationListRenderer';
        return new $class($tool_browser);
    }

    function get_tool_browser()
    {
        return $this->tool_browser;
    }

    function get_allowed_types()
    {
        return $this->tool_browser->get_allowed_types();
    }

    function get_search_condition()
    {
        return $this->tool_browser->get_search_condition();
    }

    function get_publication_conditions()
    {
        return $this->tool_browser->get_publication_conditions();
    }

    function get_user()
    {
        return $this->tool_browser->get_user();
    }

    function get_user_id()
    {
        return $this->tool_browser->get_user_id();
    }

    function get_course_id()
    {
        return $this->tool_browser->get_course_id();
    }

    function get_tool_id()
    {
        return $this->tool_browser->get_tool_id();
    }

    function get_publication_actions($publication, $show_move = true)
    {
        $toolbar = new Toolbar(Toolbar :: TYPE_HORIZONTAL);

        $details_url = $this->get_url(array(Tool :: PARAM_PUBLICATION_ID => $publication->get_id(), Tool :: PARAM_ACTION => Tool :: ACTION_VIEW));
        $toolbar->add_item(new ToolbarItem(Translation :: get('Details'), Theme :: get_common_image_path() . 'action_details.png', $details_url, ToolbarItem :: DISPLAY_ICON));

     	if ($publication->get_content_object() instanceof ComplexContentObjectSupport)
        {
        	$toolbar->add_item(new ToolbarItem(Translation :: get('DisplayComplex'), Theme :: get_common_image_path() . 'action_browser.png', $this->get_complex_display_url($publication->get_id()), ToolbarItem :: DISPLAY_ICON));
        }
        
        if ($this->is_allowed(EDIT_RIGHT))
        {
            $toolbar->add_item(new ToolbarItem(Translation :: get('Edit'), Theme :: get_common_image_path() . 'action_edit.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_UPDATE, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON));

            $toolbar->add_item(new ToolbarItem(Translation :: get('Delete'), Theme :: get_common_image_path() . 'action_delete.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_DELETE, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON, true));

            if ($publication->get_content_object() instanceof ComplexContentObjectSupport)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('BuildComplex'), Theme :: get_common_image_path() . 'action_bar.png', $this->get_complex_builder_url($publication->get_id()), ToolbarItem :: DISPLAY_ICON));
            }

            if ($show_move && $this->get_publication_count() > 1)
            {
                if ($publication->get_display_order_index() > 1)
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUp'), Theme :: get_common_image_path() . 'action_up.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_UP, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON));
                }
                else
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('MoveUpNA'), Theme :: get_common_image_path() . 'action_up_na.png', null, ToolbarItem :: DISPLAY_ICON));
                }

                if ($publication->get_display_order_index() < $this->get_publication_count())
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDown'), Theme :: get_common_image_path() . 'action_down.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_DOWN, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON));
                }
                else
                {
                    $toolbar->add_item(new ToolbarItem(Translation :: get('MoveDownNA'), Theme :: get_common_image_path() . 'action_down_na.png', null, ToolbarItem :: DISPLAY_ICON));
                }
            }

            $visibility_url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_TOGGLE_VISIBILITY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()));
            if ($publication->is_hidden())
            {
                $visibility_image = 'action_invisible.png';
            }
            elseif ($publication->is_forever())
            {
                $visibility_image = 'action_visible.png';
            }
            else
            {
                $visibility_image = 'action_period.png';
                $visibility_url = '#';
            }

            $toolbar->add_item(new ToolbarItem(Translation :: get('Visible'), Theme :: get_common_image_path() . $visibility_image, $visibility_url, ToolbarItem :: DISPLAY_ICON));

            if ($this->get_tool_browser() instanceof Categorizable)
            {
                $toolbar->add_item(new ToolbarItem(Translation :: get('Move'), Theme :: get_common_image_path() . 'action_move.png', $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_MOVE_TO_CATEGORY, Tool :: PARAM_PUBLICATION_ID => $publication->get_id())), ToolbarItem :: DISPLAY_ICON));
            }

        }
        
        if (WebApplication :: is_active('gradebook'))
        {
            require_once dirname(__FILE__) . '/../../gradebook/evaluation_manager/evaluation_manager.class.php';
            $internal_item = EvaluationManager :: retrieve_internal_item_by_publication(WeblcmsManager :: APPLICATION_NAME, $publication->get_id());
            if ($internal_item && $internal_item->get_calculated() != 1)
            {
                $evaluate_url = $this->get_url(array(Tool :: PARAM_ACTION => Tool :: ACTION_EVALUATE_TOOL_PUBLICATION, Tool :: PARAM_PUBLICATION_ID => $publication->get_id()));

                $toolbar->add_item(new ToolbarItem(Translation :: get('Evaluate'), Theme :: get_common_image_path() . 'action_evaluation.png', $evaluate_url, ToolbarItem :: DISPLAY_ICON));
            }
        }

        if (method_exists($this->get_tool_browser()->get_parent(), 'get_content_object_publication_actions'))
        {

            $content_object_publication_actions = $this->get_tool_browser()->get_parent()->get_content_object_publication_actions($publication);
            $toolbar->add_items($content_object_publication_actions);
        }

        return $toolbar;
    }
}
?>
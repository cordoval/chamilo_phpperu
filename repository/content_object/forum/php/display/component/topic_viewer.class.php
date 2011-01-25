<?php
namespace repository\content_object\forum;

use repository\ContentObject;

use repository\RepositoryManager;

use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Path;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;
use common\libraries\ActionBarRenderer;
use common\libraries\ToolbarItem;
use common\libraries\Toolbar;
use common\libraries\Theme;
use common\libraries\Application;
use common\libraries\DatetimeUtilities;
use common\libraries\ObjectTableOrder;

use user\UserDataManager;

use repository\ComplexDisplay;
use repository\RepositoryDataManager;
use repository\ComplexContentObjectItem;
use repository\content_object\forum_topic\ForumTopic;
use HTML_Table;

/**
 * $Id: topic_viewer.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_display.forum.component
 */
require_once dirname(__FILE__) . '/../forum_display.class.php';

class ForumDisplayTopicViewerComponent extends ForumDisplay
{
    private $action_bar;
    private $posts;
    private $topic;
    private $is_locked;

    function run()
    {
        $topic = $this->get_complex_content_object_item();
            $this->retrieve_children($topic->get_ref());

        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_FORUM)), $this->get_root_content_object()->get_title()));

        $forums = $this->retrieve_children_trail($this->get_root_content_object());
        while ($forums)
        {
            $forum = $forums[0]->get_ref();
            if ($forum->get_id() != $topic->get_parent() && $this->get_root_content_object_id() != $topic->get_parent())
            {
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $forums[0]->get_id(), ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_FORUM)), $forum->get_title()));
                $forums = $this->retrieve_children_trail($forum);
            }
            else
            {
                $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $forums[0]->get_id(), ComplexDisplay :: PARAM_DISPLAY_ACTION => ForumDisplay :: ACTION_VIEW_FORUM)), $forum->get_title()));
                $forums = null;
            }
        }

        $trail->add(new Breadcrumb($this->get_url(array(ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => $topic->get_id())), $this->posts[0]->get_ref()->get_title()));

        $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($topic->get_ref(), ForumTopic :: get_type_name());
        $this->topic = $content_object;
        $this->is_locked = $content_object->is_locked();

        if(!$this->is_locked)
        {
        	$this->action_bar = $this->get_action_bar();
        }
        $table = $this->get_posts_table();

        $this->display_header();

        echo '<a name="top"></a>';

    	if($this->action_bar)
        {
        	echo $this->action_bar->as_html();
        }

        echo '<div class="clear"></div><br />';
        echo $table->toHtml();
        echo '<br />';

        $this->display_footer();

        $this->forum_topic_viewed($this->get_complex_content_object_item_id());
    }

    private function retrieve_children_trail($forum)
    {
        $rdm = RepositoryDataManager :: get_instance();

        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $forum->get_id(), ComplexContentObjectItem :: get_table_name()));
        while ($child = $children->next_result())
        {
            $lo = $rdm->retrieve_content_object($child->get_ref());
            $child->set_ref($lo);
            if ($lo->get_type() != ForumTopic :: get_type_name())
            {
                $forums[] = $child;
            }
        }

        return $forums;
    }

    function retrieve_children($topic)
    {
        $rdm = RepositoryDataManager :: get_instance();

        $children = $rdm->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $topic, ComplexContentObjectItem :: get_table_name()), array(new ObjectTableOrder(ComplexContentObjectItem :: PROPERTY_ADD_DATE, SORT_ASC)));
        while ($child = $children->next_result())
        {
            $post = $rdm->retrieve_content_object($child->get_ref());
            $child->set_ref($post);
            $this->posts[] = $child;
        }
    }

    function get_posts_table()
    {
        $table = new HTML_Table(array('class' => 'forum', 'cellspacing' => 1));

        $this->create_posts_table_header($table);
        $row = 2;
        $this->create_posts_table_content($table, $row);

        $this->create_posts_table_footer($table, $row);

        return $table;
    }

    function create_posts_table_header($table)
    {
        $table->setCellContents(0, 0, '');
        $table->setCellAttributes(0, 0, array('colspan' => 2, 'class' => 'category'));

        $table->setHeaderContents(1, 0, Translation :: get('Author'));
        $table->setCellAttributes(1, 0, array('width' => 130));
        $table->setHeaderContents(1, 1, Translation :: get('Message'));
    }

    function create_posts_table_footer($table, $row)
    {
        $table->setCellContents($row, 0, '');
        $table->setCellAttributes($row, 0, array('colspan' => 2, 'class' => 'category'));
    }

    function create_posts_table_content($table, &$row)
    {
        $udm = UserDataManager :: get_instance();

        $post_counter = 0;

        foreach ($this->posts as $post)
        {
            $class = ($post_counter % 2 == 0 ? 'row1' : 'row2');

            $user = $udm->retrieve_user($post->get_user_id());
            $table->setCellContents($row, 0, '<a name="post_' . $post->get_id() . '"></a><b>' . $user->get_fullname() . '</b>');
            $table->setCellAttributes($row, 0, array('class' => $class, 'width' => 150, 'valign' => 'middle', 'align' => 'center'));
            $table->setCellContents($row, 1, '<b>' . Translation :: get('Subject') . ':</b> ' . $post->get_ref()->get_title());
            $table->setCellAttributes($row, 1, array('class' => $class, 'height' => 25, 'style' => 'padding-left: 10px;'));

            $row ++;

            $info = '<br /><img style="max-width: 100px;" src="' . $user->get_full_picture_url() . '" /><br /><br />' . DatetimeUtilities :: format_locale_date(null, $post->get_add_date());
            $message = $this->format_message($post->get_ref()->get_description());

            $attachments = $post->get_ref()->get_attached_content_objects();

            if (count($attachments) > 0)
            {
                $message .= '<div class="quotetitle">' . Translation :: get('Attachments') . ':</div><div class="quotecontent"><ul>';

                foreach ($attachments as $attachment)
                {
                    $url = Path :: get_launcher_application_path(true) . 'index.php?' . Application :: PARAM_APPLICATION . '=attachment_viewer&' . RepositoryManager :: PARAM_CONTENT_OBJECT_ID . '=' . $attachment->get_id();
                	$url = 'javascript:openPopup(\'' . $url . '\'); return false;';
                	$message .= '<li><a href="#" onClick="' . $url . '"><img src="' . Theme :: get_common_image_path() . 'treemenu_types/' . $attachment->get_type() . '.png" alt="' . htmlentities(Translation :: get(ContentObject :: type_to_class($attachment->get_type()) . 'TypeName')) . '"/> ' . $attachment->get_title() . '</a></li>';
                }

                $message .= '</ul></div>';
            }

            $table->setCellContents($row, 0, $info);
            $table->setCellAttributes($row, 0, array('class' => $class, 'align' => 'center', 'valign' => 'top', 'height' => 150));
            $table->setCellContents($row, 1, $message);
            $table->setCellAttributes($row, 1, array('class' => $class, 'valign' => 'top', 'style' => 'padding: 10px; padding-top: 10px;'));

            $row ++;

            $bottom_bar = array();
            $bottom_bar[] = '<div style="float: left; padding-top: 4px;">';

            $object = $post->get_ref();
			if($object->get_creation_date() != $object->get_modification_date())
			{
            	$bottom_bar[] = Translation :: get('LastChangedAt', array('TIME' => DatetimeUtilities :: format_locale_date(Translation :: get('DateFormatShort', null , Utilities :: COMMON_LIBRARIES) . ', ' . Translation :: get('TimeNoSecFormat', null , Utilities :: COMMON_LIBRARIES), $object->get_modification_date()) ));
			}

            $bottom_bar[] = '</div>';
            $bottom_bar[] = '<div style="float: right;">';
            $bottom_bar[] = $this->get_post_actions($post);
            $bottom_bar[] = '</div>';

            $table->setCellContents($row, 0, '<a href="#top"><small>' . Translation :: get('Top') . '</small></a>');
            $table->setCellAttributes($row, 0, array('class' => $class, 'style' => 'padding: 5px;'));
            $table->setCellContents($row, 1, implode("\n", $bottom_bar));
            $table->setCellAttributes($row, 1, array('class' => $class, 'align' => 'right', 'style' => 'padding: 5px;'));

            $row ++;

            $table->setCellContents($row, 0, ' ');
            $table->setCellAttributes($row, 0, array('colspan' => '2', 'class' => 'spacer'));

            $row ++;

            $post_counter ++;
        }
    }

    private function format_message($message)
    {
    	$message = preg_replace('/\[quote=("|&quot;)(.*)("|&quot;)\]/', "<div class=\"quotetitle\">$2 " . Translation :: get('Wrote') . ":</div><div class=\"quotecontent\">", $message);
        $message = str_replace('[/quote]', '</div>', $message);

        return $message;
    }

    function get_post_actions($complex_content_object_item)
    {
        $post = $complex_content_object_item->get_ref();

        $toolbar = new Toolbar();


        $parameters = array();
        $parameters[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $parameters[ComplexDisplay :: PARAM_SELECTED_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $complex_content_object_item->get_id();

        if(!$this->is_locked)
        {
	        $parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_QUOTE_FORUM_POST;

	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Quote'),
	        		Theme :: get_image_path() . 'buttons/icon_post_quote.gif',
					$this->get_url($parameters),
					ToolbarItem :: DISPLAY_ICON
			));

	        $parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_CREATE_FORUM_POST;

	        $toolbar->add_item(new ToolbarItem(
	        		Translation :: get('Reply'),
	        		Theme :: get_image_path() . 'buttons/button_pm_reply.gif',
					$this->get_url($parameters),
					ToolbarItem :: DISPLAY_ICON
			));
        }

        if ($this->get_parent()->is_allowed(EDIT_RIGHT) || $complex_content_object_item->get_user_id() == $this->get_user_id())
        {
            $parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_EDIT_FORUM_POST;
            $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Edit', null , Utilities :: COMMON_LIBRARIES),
        		Theme :: get_image_path() . 'buttons/icon_post_edit.gif',
				$this->get_url($parameters),
				ToolbarItem :: DISPLAY_ICON
			));
        }

        if ($this->get_parent()->is_allowed(DELETE_RIGHT) || $complex_content_object_item->get_user_id() == $this->get_user_id())
        {
            $parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_DELETE_FORUM_POST;
             $toolbar->add_item(new ToolbarItem(
        		Translation :: get('Delete', null , Utilities :: COMMON_LIBRARIES),
        		Theme :: get_image_path() . 'buttons/icon_post_delete.gif',
				$this->get_url($parameters),
				ToolbarItem :: DISPLAY_ICON,
				true
			));
        }

        return $toolbar->as_html();

    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $parameters = array();
        $parameters[ComplexDisplay :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item_id();
        $parameters[ComplexDisplay :: PARAM_DISPLAY_ACTION] = ForumDisplay :: ACTION_CREATE_FORUM_POST;

        $action_bar->add_common_action(new ToolbarItem(Translation :: get('ReplyOnTopic', null , 'repository\content_object\forum_topic'), Theme :: get_common_image_path() . 'action_reply.png', $this->get_url($parameters), ToolbarItem :: DISPLAY_ICON_AND_LABEL));
       	return $action_bar;
    }

}
?>
<?php
namespace repository\content_object\forum;

use common\libraries\SubselectCondition;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\BreadcrumbTrail;
use common\libraries\EqualityCondition;
use common\libraries\AndCondition;
use repository\RepositoryDataManager;
use repository\ContentObjectDisplay;
use repository\ComplexBuilder;
use repository\ContentObject;
use repository\content_object\forum_post\ForumPost;
use repository\ComplexContentObjectItem;
use repository\ComplexBrowserTable;
use repository\content_object\forum_topic\ForumTopic;
use repository\content_object\forum_topic\ComplexForumTopic;
use repository\ContentObjectTypeSelectorSupport;

/**
 * $Id: browser.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.complex_builder.forum.component
 */

require_once dirname(__FILE__) . '/browser/forum_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/browser/forum_browser_table_column_model.class.php';
require_once dirname(__FILE__) . '/browser/forum_post_browser_table_cell_renderer.class.php';
require_once dirname(__FILE__) . '/browser/forum_post_browser_table_column_model.class.php';

class ForumBuilderBrowserComponent extends ForumBuilder implements ContentObjectTypeSelectorSupport
{

    function run()
    {
        $menu_trail = $this->get_complex_content_object_breadcrumbs();
        $trail = BreadcrumbTrail :: get_instance();
        $trail->merge($menu_trail);
        $trail->add_help('repository forum builder');

        if ($this->get_complex_content_object_item())
        {
            $content_object = RepositoryDataManager :: get_instance()->retrieve_content_object($this->get_complex_content_object_item()->get_ref());
        }
        else
        {
            $content_object = $this->get_root_content_object();
        }

        $this->display_header($trail);

        $display = ContentObjectDisplay :: factory($this->get_root_content_object());
        echo $display->get_full_html();

        echo '<br />';
        echo $this->get_creation_links($content_object);
        echo '<div class="clear">&nbsp;</div><br />';

        echo $this->get_table_html();

        echo '<div class="clear">&nbsp;</div>';

        $this->display_footer();
    }

    function get_table_html()
    {

        $parameters = array(ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID => ($this->get_complex_content_object_item() ? $this->get_complex_content_object_item()->get_id() : null));

        if ($this->get_complex_content_object_item())
        {
            $parameters[ComplexBuilder :: PARAM_COMPLEX_CONTENT_OBJECT_ITEM_ID] = $this->get_complex_content_object_item()->get_id();
        }

        if($this->get_complex_content_object_item() instanceof ComplexForumTopic)
        {
        	$conditions = array();
	        $conditions[] = $this->get_complex_content_object_table_condition();
	        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, ForumPost :: get_type_name());
	        $conditions[] = new SubselectCondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subcondition);
	        $condition = new AndCondition($conditions);

	        $html[] = '<h3>' . Translation :: get('Posts') . '</h3>';
	        $table = new ComplexBrowserTable($this, array_merge($this->get_parameters(), $parameters), $condition, true, new ForumPostBrowserTableColumnModel($this), new ForumPostBrowserTableCellRenderer($this, $condition));
	        $html[] = $table->as_html();
        }
        else
        {
	        $conditions = array();
	        $conditions[] = $this->get_complex_content_object_table_condition();
	        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Forum :: get_type_name());
	        $conditions[] = new SubselectCondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subcondition);
	        $condition = new AndCondition($conditions);

	        $html[] = '<h3>' . Translation :: get('Forums') . '</h3>';
	        $table = new ComplexBrowserTable($this, array_merge($this->get_parameters(), $parameters), $condition, true, null, null, 'forum_table');
	        $html[] = $table->as_html();

	        $conditions = array();
	        $conditions[] = $this->get_complex_content_object_table_condition();
	        $subcondition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, ForumTopic :: get_type_name());
	        $conditions[] = new SubselectCondition(ComplexContentObjectItem :: PROPERTY_REF, ContentObject :: PROPERTY_ID, 'content_object', $subcondition);
	        $condition = new AndCondition($conditions);

	        $html[] = '<br /><h3>' . Translation :: get('ForumTopics') . '</h3>';
	        $table = new ComplexBrowserTable($this, array_merge($this->get_parameters(), $parameters), $condition, true, new ForumBrowserTableColumnModel($this), new ForumBrowserTableCellRenderer($this, $condition), 'topic_table');
	        $html[] = $table->as_html();
        }

        return implode("\n", $html);
    }
}

?>
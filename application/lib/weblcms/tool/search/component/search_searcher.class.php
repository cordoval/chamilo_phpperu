<?php
/**
 * $Id: search_searcher.class.php 216 2009-11-13 14:08:06Z kariboe $
 * @package application.lib.weblcms.tool.search.component
 */
require_once dirname(__FILE__) . '/../search_tool_component.class.php';
require_once 'Pager/Pager.php';
/**
 * Tool to search in the course.
 * @todo: Link from search results to location in course
 * @todo: Advanced search (only in recent publications, only certain types of
 * publications, only in a given tool,...)
 */
class SearchToolSearcherComponent extends SearchToolComponent
{
    /**
     * Number of results per page
     */
    const RESULTS_PER_PAGE = 10;
    private $action_bar;

    // Inherited
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add_help('courses search tool');

        $this->action_bar = $this->get_action_bar();
        $this->display_header($trail, true);
        // Display the search form
        //$form = new SearchForm($this);
        echo '<div style="text-align:center">';
        echo $this->action_bar->as_html();
        echo '</div>';
        // If form validates, show results
        if ($query = $this->get_query())
        {
            $datamanager = WeblcmsDataManager :: get_instance();

        	$user_id = $this->get_user_id();
            $course_groups = $this->get_course_groups();

            $course_group_ids = array();

            foreach($course_groups as $course_group)
            {
              	$course_group_ids[] = $course_group->get_id();
            }

            $conditions = array();
            $conditions[] = new EqualityCondition(ContentObjectPublication :: PROPERTY_COURSE_ID, $this->get_course_id());

            $access = array();
            $access[] = new InCondition('user_id', $user_id, $datamanager->get_database()->get_alias('content_object_publication_user'));
            $access[] = new InCondition('course_group_id', $course_group_ids, $datamanager->get_database()->get_alias('content_object_publication_course_group'));
            if (! empty($user_id) || ! empty($course_group_ids))
            {
                $access[] = new AndCondition(array(new EqualityCondition('user_id', null, $datamanager->get_database()->get_alias('content_object_publication_user')), new EqualityCondition('course_group_id', null, $datamanager->get_database()->get_alias('content_object_publication_course_group'))));
            }
            $conditions[] = new OrCondition($access);
            $condition = new AndCondition($conditions);
            $publications = $datamanager->retrieve_content_object_publications_new($condition);
            $tools = array();

            while ($publication = $publications->next_result())
            {
                $tools[$publication->get_tool()][] = $publication;
            }

            $results = 0;
            foreach ($tools as $tool => $publications)
            {
                if (strpos($tool, 'feedback') !== false)
                    continue;

                $objects = array();

                foreach ($publications as $publication)
                {
                    $lo = $publication->get_content_object();
                    $lo_title = $lo->get_title();
                    $lo_description = strip_tags($lo->get_description());

                    if (stripos($lo_title, $query) !== false || stripos($lo_description, $query) !== false)
                        $objects[] = $publication;

                }
                $count = count($objects);
                if ($count > 0)
                {
                    $html[] = '<h4>' . Translation :: get(ucfirst($tool) . 'ToolTitle') . ' (' . $count . ' ' . Translation :: get('result(s)') . ') </h4>';
                    $results += $count;

                    foreach ($objects as $index => $pub)
                    {
                        $object = $pub->get_content_object();
                        if($object->get_type() != Introduction :: get_type_name())
                        {
                        	$url = $this->get_url(array(WeblcmsManager :: PARAM_TOOL => $tool, Tool :: PARAM_PUBLICATION_ID => $pub->get_id(), Tool :: PARAM_ACTION => 'view'));
                        }
                        else
                        {
                        	$url = '#';
                        }
                        
                        $html[] = '<div class="content_object" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $object->get_icon_name() . '.png);">';
                        $html[] = '<div class="title"><a href="' . $url . '">' . Text :: highlight($object->get_title(), $query, 'yellow') . '</a></div>';
                        $html[] = '<div class="description">' . Text :: highlight(strip_tags($object->get_description()), $query, 'yellow') . '</div>';
                        $html[] = '</div>';
                    }
                }
            }

            if ($results == 0)
            {
                $html[] = Translation :: get('NoSearchResults');
            }

            echo $results . ' ' . Translation :: get('ResultsFoundFor') . ' <span style="background-color: yellow;">' . $query . '</span>';
            echo implode("\n", $html);
        }
        $this->display_footer();

    }

    private static function create_pager($total, $per_page)
    {
        $params = array();
        $params['mode'] = 'Sliding';
        $params['perPage'] = $per_page;
        $params['totalItems'] = $total;
        return Pager :: factory($params);
    }

    private static function get_pager_links($pager)
    {
        return '<div style="text-align: center; margin: 1em 0;">' . $pager_links .= $pager->links . '</div>';
    }

    function get_action_bar()
    {
        $action_bar = new ActionBarRenderer(ActionBarRenderer :: TYPE_HORIZONTAL);

        $action_bar->set_search_url($this->get_url(array(Tool :: PARAM_ACTION => SearchTool :: ACTION_SEARCH)));

        return $action_bar;
    }

    function get_condition()
    {
        $query = $this->get_query();
        if (! $query)
        {
        	$query = Request :: post('query');
        }

        if (isset($query) && $query != '')
        {
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_TITLE, '*' . $query . '*');
            $conditions[] = new PatternMatchCondition(ContentObject :: PROPERTY_DESCRIPTION, '*' . $query . '*');
            return new OrCondition($conditions);
        }

        return null;
    }

    function get_query()
    {
        $query = trim($this->action_bar->get_query());
        if($query == '')
        {
        	return null;
        }
        
        return $query;
    }
}
?>
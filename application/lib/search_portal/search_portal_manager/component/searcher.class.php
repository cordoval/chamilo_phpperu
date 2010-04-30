<?php
/**
 * $Id: search_portal_manager_searcher_component.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_portal_manager.component
 */
require_once dirname(__FILE__) . '/../../search_source/search_source.class.php';
require_once 'Pager/Pager.php';


class SearchPortalManagerSearcherComponent extends SearchPortalManager
{
    const PARAM_QUERY = 'query';
    const PARAM_URL = 'url';
    
    const CONTENT_OBJECTS_PER_PAGE = 10;

    /*
	 * Inherited.
	 */
    function run()
    {
        $trail = new BreadcrumbTrail();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SearchPortal')));
        
        $query = trim(Request :: get('query'));
        
        if ($query && $query != '')
            $trail->add(new Breadcrumb($this->get_url(array('query' => Request :: get('query'), 'submit' => 'Search')), Translation :: get('SearchResultsFor') . ' ' . Request :: get('query')));
            
        Display :: header($trail);
        Display :: tool_title(Translation :: get('SearchPortal'));
        
        $form = new FormValidator('search_simple', 'get', $this->get_url(), '', null, false);
        $form->addElement('text', self :: PARAM_QUERY, '', 'size="40" class="search_query_no_icon" id="inputString" onkeyup="lookup(this.value);"');
        //$form->addElement('submit', 'submit', Translation :: get('Search'));
        

        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Search'), array('class' => 'normal search'));
        //$buttons[] = $this->createElement('style_reset_button', 'reset', Translation :: get('Reset'), array('class' => 'normal empty'));
        

        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        
        $form->addElement('hidden', 'application');
        if ($supports_remote)
        {
            $form->addElement('static', null, null, '<span id="url_expander" style="font-size: 90%;">[<a href="javascript:void(0);" onclick="expandRemoteSearch();">' . Translation :: get('RemoteRepository') . '</a>]</span>');
            $form->addElement('static', null, null, '<div id="url_container" style="display: none; margin-top: 0.25em;">');
            $form->addElement('text', self :: PARAM_URL, Translation :: get('RepositoryURL'), 'size="50"');
            $form->addElement('static', null, null, '</div>');
        }
        echo '<div style="text-align: center; margin: 0 0 2em 0;">';
        $renderer = clone $form->defaultRenderer();
        $renderer->setElementTemplate('{label} {element} ');
        $form->accept($renderer);
        $form->setDefaults(array('application' => 'search_portal'));
        echo $renderer->toHTML();
        echo '</div>';
        
        if ($form->validate())
        {
            $form_values = $form->exportValues();
            $query = trim($form_values[self :: PARAM_QUERY]);
            if (! empty($query) && $query != '')
            {
                $url = trim($form_values[self :: PARAM_URL]);
                if (! empty($url))
                {
                    echo <<<END
<script type="text/javascript">
/* <![CDATA[ */
expandRemoteSearch();
/* ]]> */
</script>
END;
                }
                self :: search($query, $url);
            }
        }
        Display :: footer();
    }

    /**
     * Renders the search portal block and returns it.
     */
    function render_block($block)
    {
        $search_portal_block = SearchPortalBlock :: factory($this, $block);
        return $search_portal_block->run();
    }

    private static function search($query, $url)
    {
        $search_source = self :: get_search_source($url);
        if ($search_source instanceof Exception)
        {
            self :: report_exception($search_source);
        }
        else
        {
            $result = self :: perform_search($query, $search_source);
            if ($result instanceof Exception)
            {
                self :: report_exception($result);
            }
            else
            {
                $repository_title = $result->get_repository_title();
                $repository_url = $result->get_repository_url();
                $result_count = $result->get_actual_result_count();
                $results = $result->get_returned_results();
                $count = $results->size();
                if ($count)
                {
                    $pager = self :: create_pager($count, self :: CONTENT_OBJECTS_PER_PAGE);
                    $pager_links = self :: get_pager_links($pager);
                    $offset = $pager->getOffsetByPageId();
                    $first = $offset[0] - 1;
                    $results->skip($first);
                    $str = htmlentities(str_ireplace(array('%first%', '%last%', '%total%'), array($offset[0], $offset[1], $count), Translation :: get('Results_Through_Of_From_')));
                    $str = str_ireplace('%repository%', '<a href="' . htmlentities($repository_url) . '">' . htmlspecialchars($repository_title) . '</a>', $str);
                    echo '<h3>' . $str . '</h3>';
                    if ($result_count > $count)
                    {
                        $str = str_ireplace(array('%returned%', '%actual%'), array($count, $result_count), Translation :: get('TheRepositoryReturnedOnly_Of_Results'));
                        echo '<p><strong>' . htmlentities(Translation :: get('Notice')) . ':</strong> ' . htmlentities($str) . '</p>';
                    }
                    echo $pager_links;
                    $i = 0;
                    echo '<ul class="portal_search_results">';
                    while ($i ++ < self :: CONTENT_OBJECTS_PER_PAGE && $object = $results->next_result())
                    {
                        self :: display_result($object);
                    }
                    echo '</ul>';
                    echo $pager_links;
                }
                else
                {
                    echo '<p>' . htmlentities(Translation :: get('NoResultsFound')) . '</p>';
                }
            }
        }
    }

    private static function report_exception($exception)
    {
        echo '<p><strong>' . htmlentities(Translation :: get('Error')) . ':</strong> ' . htmlentities($exception->getMessage()) . '</p>';
    }

    private static function display_result($object)
    {
        $query = Request :: get('query');
        $object->set_title(Text :: highlight($object->get_title(), $query, 'yellow'));
        $object->set_description(Text :: highlight($object->get_description(), $query, 'yellow'));
        
        
        /*
		 * This pretty much makes every GIF file accessible, which is evil.
		 * Type GIFs should be in a separate directory.
		 */
        echo '<li class="portal_search_result" style="background-image: url(', Theme :: get_common_image_path() . 'content_object/' . $object->get_type() . '.png);">';
        //echo '<div class="portal_search_result_title"><a href="'.htmlentities($object->get_view_url()).'">'.htmlspecialchars($object->get_title()).'</a></div>';
        echo '<div class="portal_search_result_title">' . $object->get_title() . '</div>';
        /*
		 * We can't guarantee types from remote repositories will be registered
		 * locally, so all the formatting we do is remove underscores.
		 */
        echo '<div class="portal_search_result_type">' . str_replace('_', ' ', $object->get_type()) . '</div>';
        echo '<div class="portal_search_result_description">' . $object->get_description() . '</div>';
        echo '<div class="portal_search_result_owner">'. Translation :: get('ObjectOwner') . ': ' . UserDataManager :: get_instance()->retrieve_user($object->get_owner_id())->get_fullname() . '</div>';
    	if(PlatformSetting :: get('active_online_email_editor'))
        {
        	//include email editor here
        }
        echo '<div class="portal_search_result_date">'. Translation :: get('LastModification') . ': ' . date('r', $object->get_modification_date()) . '</div>';
        echo '</li>';
    }

    private static function get_pager_links($pager)
    {
        return '<div style="text-align: center; margin: 1em 0;">' . $pager_links .= $pager->links . '</div>';
    }

    private static function create_pager($total, $per_page)
    {
        $params = array();
        $params['mode'] = 'Sliding';
        $params['perPage'] = $per_page;
        $params['totalItems'] = $total;
        return Pager :: factory($params);
    }

    private static function perform_search($query, $search_source)
    {
        try
        {
            return $search_source->search($query);
        }
        catch (Exception $ex)
        {
            return $ex;
        }
    }

    private static function get_search_source($url)
    {
        return SearchSource :: factory('local_repository');
    }

}
?>
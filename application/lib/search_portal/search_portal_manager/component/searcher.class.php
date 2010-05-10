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
    const CONTENT_OBJECTS_PER_PAGE = 10;

    /*
	 * Inherited.
	 */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('SearchPortal')));
        
        $query = trim(Request :: get('query'));
        
        if ($query && $query != '')
        {
            $trail->add(new Breadcrumb($this->get_url(array('query' => Request :: get('query'), 'submit' => 'Search')), Translation :: get('SearchResultsFor') . ' ' . Request :: get('query')));
        }
            
        $this->display_header($trail);
        
        $form = new FormValidator('search_simple', 'get', $this->get_url(), '', null, false);
        
        $form->addElement('text', self :: PARAM_QUERY, '', 'size="40" class="search_query_no_icon" id="search_query"');
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Search'), array('class' => 'normal search'));
        $form->addGroup($buttons, 'buttons', null, '&nbsp;', false);
        $form->addElement('hidden', 'application');
        
        $renderer = clone $form->defaultRenderer();
        $renderer->setElementTemplate('{label} {element} ');
        
        $form->accept($renderer);
        $form->setDefaults(array('application' => 'search_portal'));
        
        $html[] = '<div style="text-align: center; margin: 0 0 2em 0;">';
        
        $html[] = $renderer->toHTML();
        $html[] = '</div>';
        
        echo implode("\n", $html);
         
        if ($form->validate())
        {
            $form_values = $form->exportValues();
            $query = trim($form_values[self :: PARAM_QUERY]);
            if (! empty($query) && $query != '')
            {
               $this->search($query);
            }
        }
        
    	$this->display_footer();
    }

    private function search($query)
    {
        $search_source = SearchSource :: factory('local_repository');
        
        if ($search_source instanceof Exception)
        {
            $this->report_exception($search_source);
        }
        else
        {
        	$count = $search_source->count_search_results($query);
            if ($count)
            {
            	$pager = $this->create_pager($count, self :: CONTENT_OBJECTS_PER_PAGE);  
                $pager_links = $this->get_pager_links($pager);
                $offset = $pager->getOffsetByPageId();
                $first = $offset[0] - 1;
                
                $str = htmlentities(str_ireplace(array('%first%', '%last%', '%total%'), array($offset[0], $offset[1], $count), Translation :: get('Results_Through_Of_From_')));
                
                $html[] = '<h3>' . $str . '</h3>';
                $html[] = $pager_links;
                
                $i = 0;
                $html[] = '<ul class="portal_search_results">';
                
                $results = $search_source->retrieve_search_results($query, $first, self :: CONTENT_OBJECTS_PER_PAGE);
                
                foreach($results as $result)
                {
                	$html[] = $this->display_result($result);
                }
                
                $html[] = '</ul>';
                $html[] = $pager_links;
                
                echo implode("\n", $html);
            }
            else
            {
                echo '<p>' . htmlentities(Translation :: get('NoResultsFound')) . '</p>';
            }
        }
    }

    private function report_exception($exception)
    {
        echo '<p><strong>' . htmlentities(Translation :: get('Error')) . ':</strong> ' . htmlentities($exception->getMessage()) . '</p>';
    }

    private function display_result($object)
    {
        $query = Request :: get('query');
        $object->set_title(Text :: highlight($object->get_title(), $query, 'yellow'));
        $object->set_description(Text :: highlight($object->get_description(), $query, 'yellow'));

        $html = array();
        $html[] = '<li class="portal_search_result" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $object->get_type() . '.png);">';
        $html[] = '<div class="portal_search_result_title">' . $object->get_title() . '</div>';
        $html[] = '<div class="portal_search_result_type">' . str_replace('_', ' ', $object->get_type()) . '</div>';
        $html[] = '<div class="portal_search_result_description">' . $object->get_description() . '</div>';
        $html[] = '<div class="portal_search_result_owner">'. Translation :: get('ObjectOwner') . ': ' . UserDataManager :: get_instance()->retrieve_user($object->get_owner_id())->get_fullname() . '</div>';
        
    	if(PlatformSetting :: get('active_online_email_editor'))
        {
        	//include email editor here
        }
        
        $html[] = '<div class="portal_search_result_date">'. Translation :: get('LastModification') . ': ' . DatetimeUtilities :: format_locale_date(null, $object->get_modification_date()) . '</div>';
        $html[] = '</li>';
        
        return implode("\n", $html);
    }

    private function get_pager_links($pager)
    {
        return '<div style="text-align: center; margin: 1em 0;">' . $pager_links .= $pager->links . '</div>';
    }

    private function create_pager($total, $per_page)
    {
        $params = array();
        $params['mode'] = 'Sliding';
        $params['perPage'] = $per_page;
        $params['totalItems'] = $total;
        return Pager :: factory($params);
    }

    private function perform_search($query, $search_source)
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

}
?>
<?php
namespace application\search_portal;

use common\libraries\WebApplication;
use common\libraries\Breadcrumb;
use common\libraries\BreadcrumbTrail;
use common\libraries\Request;
use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Utilities;
use common\libraries\Text;
use common\libraries\Theme;
use common\libraries\PlatformSetting;
use common\libraries\DatetimeUtilities;

use Pager;
use Exception;

use user\UserDataManager;

/**
 * $Id: search_portal_manager_searcher_component.class.php 222 2009-11-13 14:39:28Z chellee $
 * @package application.search_portal.search_portal_manager.component
 */

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

        $query = trim(Request :: get('query'));

        if ($query && $query != '')
        {
            $trail->add(new Breadcrumb($this->get_url(array('query' => Request :: get('query'), 'submit' => 'Search')), Translation :: get('SearchResultsFor', null , Utilities :: COMMON_LIBRARIES) . ' ' . $query));
        }

        $this->display_header();

        $form = new FormValidator('search_simple', 'get', $this->get_url(), '', null, false);

        $form->addElement('text', self :: PARAM_QUERY, '', 'size="40" class="search_query_no_icon" id="search_query"');
        $buttons[] = $form->createElement('style_submit_button', 'submit', Translation :: get('Search', null , Utilities :: COMMON_LIBRARIES), array('class' => 'normal search'));
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
        	$count = $search_source->count_search_results($query, $this->get_user());
            if ($count)
            {
            	$pager = $this->create_pager($count, self :: CONTENT_OBJECTS_PER_PAGE);
                $pager_links = $this->get_pager_links($pager);
                $offset = $pager->getOffsetByPageId();
                $first = $offset[0] - 1;

                $str = htmlentities(str_ireplace(array('%first%', '%last%', '%total%'), array($offset[0], $offset[1], $count), Translation :: get('ResultsThroughOfFrom')));

                $html[] = '<h3>' . $str . '</h3>';
                $html[] = $pager_links;

                $i = 0;
                $html[] = '<ul class="portal_search_results">';

                $results = $search_source->retrieve_search_results($query, $first, self :: CONTENT_OBJECTS_PER_PAGE, $this->get_user());

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
                echo '<p>' . htmlentities(Translation :: get('NoResults', null , Utilities :: COMMON_LIBRARIES)) . '</p>';
            }
        }
    }

    private function report_exception($exception)
    {
        echo '<p><strong>' . htmlentities(Translation :: get('Error', null , Utilities :: COMMON_LIBRARIES)) . ':</strong> ' . htmlentities($exception->getMessage()) . '</p>';
    }

    private function display_result($object)
    {
        $query = Request :: get('query');
        $object->set_title(Text :: highlight($object->get_title(), $query, 'yellow'));
        $object->set_description(Text :: highlight($object->get_description(), $query, 'yellow'));

    	$user =  UserDataManager :: get_instance()->retrieve_user($object->get_owner_id());
        if($user)
        {
        	$fullname = $user->get_fullname();
        }
        else
        {
        	$fullname = Translation :: get('UserUnknown', null , 'user');
        }

        $html = array();
        $html[] = '<li class="portal_search_result" style="background-image: url(' . Theme :: get_common_image_path() . 'content_object/' . $object->get_type() . '.png);">';
        $html[] = '<div class="portal_search_result_title">' . $object->get_title() . '</div>';
        $html[] = '<div class="portal_search_result_type">' . str_replace('_', ' ', $object->get_type()) . '</div>';
        $html[] = '<div class="portal_search_result_description">' . $object->get_description() . '</div>';
        $html[] = '<div class="portal_search_result_owner">'. Translation :: get('ObjectOwner', null , 'repository') . ': ' . $fullname . '</div>';

    	if(PlatformSetting :: get('active_online_email_editor'))
        {
        	$html[] = '<div style="float: right;"><a href="' . $this->get_email_user_url($object->get_owner_id()) . '"><img src="' . Theme :: get_common_image_path() . 'action_email.png" title="' . Translation :: get('EmailUser', null , 'user') . '"/></a></div>';
        }

        $html[] = '<div class="portal_search_result_date">'. Translation :: get('LastModification', null , Utilities :: COMMON_LIBRARIES) . ': ' . DatetimeUtilities :: format_locale_date(null, $object->get_modification_date()) . '</div>';
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

    function add_additional_breadcrumbs(BreadcrumbTrail $breadcrumbtrail)
    {
    	$breadcrumbtrail->add_help('search_portal_searcher');
    }

}
?>

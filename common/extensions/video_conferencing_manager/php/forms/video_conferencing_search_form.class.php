<?php
namespace common\extensions\video_conferencing_manager;

use common\libraries\Utilities;
use common\libraries\Request;
use common\libraries\Theme;
use common\libraries\Translation;
use common\libraries\FormValidator;

class VideoConferencingSearchForm extends FormValidator
{
    /**#@+
     * Search parameter
     */
    const PARAM_SIMPLE_SEARCH_QUERY = 'query';

    /**
     * Name of the search form
     */
    const FORM_NAME = 'search';

    /**
     * The renderer used to display the form
     */
    private $renderer;
    /**
     * Advanced or simple search form
     */
    private $advanced;

    /**
     * Creates a new search form
     * @param string $url The location to which the search request should be
     * posted.
     */
    function __construct($url)
    {
        parent :: __construct(self :: FORM_NAME, 'post', $url);
        $this->renderer = clone $this->defaultRenderer();

        $query = $this->get_query();
        if ($query)
        {
            $this->setDefaults(array(self :: PARAM_SIMPLE_SEARCH_QUERY => $query));
        }

        $this->build_simple_search_form();

        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function build_simple_search_form()
    {
        $this->renderer->setElementTemplate('<div style="vertical-align: middle; float: left;">{element}</div>');
        $this->addElement('text', self :: PARAM_SIMPLE_SEARCH_QUERY, Translation :: get('Search', null, Utilities :: COMMON_LIBRARIES), 'size="20" class="search_query"');
        $this->addElement('style_submit_button', 'submit', Theme :: get_common_image('action_search'), array('class' => 'search'));
    }

    /**
     * Display the form
     */
    function as_html()
    {
        $html = array();
        $html[] = '<div class="simple_search">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }

    /**
     * Gets the conditions that this form introduces.
     * @return String the query
     */
    function get_query()
    {
        $post_query = Request :: post(self :: PARAM_SIMPLE_SEARCH_QUERY);
        $get_query = Request :: get(self :: PARAM_SIMPLE_SEARCH_QUERY);
        if (isset($post_query))
        {
            return $post_query;
        }
        elseif (isset($get_query))
        {
            return $get_query;
        }
        else
        {
            return null;
        }
    }
}
?>
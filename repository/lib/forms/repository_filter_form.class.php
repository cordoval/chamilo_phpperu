<?php
/**
 * $Id: repository_filter_form.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.forms
 */

class RepositoryFilterForm extends FormValidator
{
    const FILTER_TYPE = 'filter_type';

    private $manager;
    private $renderer;

    /**
     * Creates a new search form
     * @param RepositoryManager $manager The repository manager in which this
     * search form will be displayed
     * @param string $url The location to which the search request should be
     * posted.
     */
    function RepositoryFilterForm($manager, $url)
    {
        parent :: __construct('repository_filter_form', 'post', $url);

        $this->renderer = clone $this->defaultRenderer();
        $this->manager = $manager;

        $this->build_form();

        $this->accept($this->renderer);
    }

    /**
     * Build the simple search form.
     */
    private function build_form()
    {
        $this->renderer->setFormTemplate('<form {attributes}><div class="filter_form">{content}</div><div class="clear">&nbsp;</div></form>');
        $this->renderer->setElementTemplate('<div class="row"><div class="formw">{label}&nbsp;{element}</div></div>');

        $rdm = RepositoryDataManager :: get_instance();
        $registrations = RepositoryDataManager :: get_registered_types();

        $filters = array();

        $filters[0] = Translation :: get('ShowAll');

        $condition = new EqualityCondition(UserView :: PROPERTY_USER_ID, $this->manager->get_user_id());
        $userviews = $rdm->retrieve_user_views($condition);

        if ($userviews->size() > 0)
        {
            $filters['c_0'] = '--------------------------';

            while ($userview = $userviews->next_result())
            {
                $filters[$userview->get_id()] = Translation :: get('View') . ': ' . $userview->get_name();
            }
        }

        $filters['c_1'] = '--------------------------';

        $hidden_types = array('learning_path_item', 'portfolio_item');

        for($i = 0; $i < count($registrations); $i ++)
        {
            if (in_array($registrations[$i], $hidden_types))
                continue;
            $filters[$registrations[$i]] = Translation :: get(Utilities :: underscores_to_camelcase($registrations[$i] . 'TypeName'));
        }

        $this->addElement('select', self :: FILTER_TYPE, null, $filters, array('class' => 'postback'));
        $this->addElement('style_submit_button', 'submit', Translation :: get('Filter'), array('class' => 'normal filter'));

        $session_filter = Session :: retrieve('filter');
        $this->setDefaults(array(self :: FILTER_TYPE => $session_filter, 'published' => 1));

        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get(WEB_PATH) . 'common/javascript/postback.js'));
    }

    function get_filter_conditions()
    {
        $session_filter = Session :: retrieve('filter');
        if ($this->validate() || isset($session_filter))
        {
            $values = $this->exportValues();
            $filter = $values[self :: FILTER_TYPE];
            if ($filter == 'c_0' || $filter == 'c_1')
                $filter = 0;

            if ($this->validate())
            {
                Session :: register('filter', $filter);
            }

            $filter_type = ! is_null($filter) ? $filter : $session_filter;

            if (is_numeric($filter_type))
            {
                if ($filter_type != '0')
                {
                    $dm = RepositoryDataManager :: get_instance();
                    $content_objects = $dm->retrieve_user_view_rel_content_objects(new EqualityCondition(UserViewRelContentObject :: PROPERTY_VIEW_ID, $filter_type));
                    while ($lo = $content_objects->next_result())
                    {
                        if ($lo->get_visibility())
                        {
                            $visible_lo[] = $lo->get_content_object_type();
                        }
                        $condition = new InCondition(ContentObject :: PROPERTY_TYPE, $visible_lo);
                    }
                }
                else
                {
                    $condition = null;
                }
            }
            else
            {
                $condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, $filter_type);
            }

            return $condition;
        }
    }

    /**
     * Display the form
     */
    function display()
    {
        $html = array();
        $html[] = '<div style="text-align: right;">';
        $html[] = $this->renderer->toHTML();
        $html[] = '</div>';
        return implode('', $html);
    }
}
?>
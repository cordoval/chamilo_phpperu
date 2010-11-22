<?php
namespace repository;

use common\libraries\FormValidator;
use common\libraries\Translation;
use common\libraries\Path;
use common\libraries\Utilities;
use common\libraries\EqualityCondition;
use common\libraries\Session;
use common\libraries\ResourceManager;
use common\libraries\InCondition;

use repository\content_object\learning_path_item\LearningPathItem;
use repository\content_object\portfolio_item\PortfolioItem;

use admin\AdminDataManager;
use admin\Registration;
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
    function __construct($manager, $url)
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

        $select = $this->addElement('select', self :: FILTER_TYPE, null, array(), array(
                'class' => 'postback'));

        $rdm = RepositoryDataManager :: get_instance();
        $registrations = RepositoryDataManager :: get_registered_types();

        $disabled_counter = 0;

        $select->addOption(Translation :: get('AllContentObjects'), 'disabled_' . $disabled_counter);
        $disabled_counter ++;

        $condition = new EqualityCondition(UserView :: PROPERTY_USER_ID, $this->manager->get_user_id());
        $userviews = $rdm->retrieve_user_views($condition);

        if ($userviews->size() > 0)
        {
            $select->addOption('--------------------------', 'disabled_' . $disabled_counter, array(
                    'disabled'));
            $disabled_counter ++;

            while ($userview = $userviews->next_result())
            {
                $select->addOption(Translation :: get('View', null, Utilities :: COMMON_LIBRARIES) . ': ' . $userview->get_name(), $userview->get_id());
            }
        }

        $select->addOption('--------------------------', 'disabled_' . $disabled_counter, array(
                'disabled'));
        $disabled_counter ++;

        $type_selector = new ContentObjectTypeSelector($this->manager, $this->get_allowed_content_object_types());
        $types = $type_selector->as_tree();

        unset($types[0]);

        foreach ($types as $key => $type)
        {
            if (is_integer($key))
            {
                $select->addOption($type, 'disabled_' . $disabled_counter, array(
                        'disabled'));
                $key = 'disabled_' . $disabled_counter;
                $disabled_counter ++;
            }
            else
            {

                $select->addOption($type, $key);
            }
        }

        $this->addElement('style_submit_button', 'submit', Translation :: get('Filter', null, Utilities :: COMMON_LIBRARIES), array(
                'class' => 'normal filter'));

        $session_filter = Session :: retrieve('filter');
        $this->setDefaults(array(
                self :: FILTER_TYPE => $session_filter,
                'published' => 1));

        $this->addElement('html', ResourceManager :: get_instance()->get_resource_html(Path :: get_web_common_libraries_path() . 'resources/javascript/postback.js'));
    }

    function get_filter_conditions()
    {
        $session_filter = Session :: retrieve('filter');
        if ($this->validate() || isset($session_filter))
        {
            $values = $this->exportValues();
            $filter = $values[self :: FILTER_TYPE];
            if (substr($filter, 0, 9) == 'disabled_')
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

    function get_allowed_content_object_types()
    {
        $types = RepositoryDataManager :: get_registered_types(true);
        foreach ($types as $index => $type)
        {
            $registration = AdminDataManager :: get_registration($type, Registration :: TYPE_CONTENT_OBJECT);
            if (! $registration || ! $registration->is_active())
            {
                unset($types[$index]);
            }
        }

        return $types;
    }
}
?>
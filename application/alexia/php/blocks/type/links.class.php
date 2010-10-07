<?php
/**
 * $Id: links.class.php 192 2009-11-13 11:51:02Z chellee $
 * @package application.lib.alexia.block
 */
require_once WebApplication :: get_application_class_path('alexia') . 'blocks/alexia_block.class.php';

class AlexiaLinks extends AlexiaBlock
{

    /**
     * Runs this component and displays its output.
     * This component is only meant for use within the home-component and not as a standalone item.
     */
    function run()
    {
        return $this->as_html();
    }

    function as_html()
    {
        $datamanager = AlexiaDataManager :: get_instance();
        $publications = $datamanager->retrieve_alexia_publications($this->get_conditions(), null, 10, new ObjectTableOrder(AlexiaPublication :: PROPERTY_FROM_DATE, SORT_DESC));

        $html = array();

        $html[] = $this->display_header();
        $html[] = Translation :: get('AlexiaLinkBlockIntroduction');
        $html[] = '<br /><br />';
        $html[] = '<div class="tool_menu">';
        $html[] = '<ul>';
        while ($publication = $publications->next_result())
        {
            $link = $publication->get_publication_object();
            $html[] = '<li class="tool_list_menu" style="background-image: url(' . Theme :: get_common_image_path() . 'treemenu_types/link.png)"><a href="' . $link->get_url() . '">' . $link->get_title() . '</a></li>';
        }
        $html[] = '</ul>';
        $html[] = '<div class="clear"></div>';
        $html[] = '</div>';
        $html[] = '<div style="clear: both; text-align: right;"><a href="' . Redirect :: get_link('alexia') . '">' . Translation :: get('MoreLinks') . '&hellip;</a></div>';
        $html[] = $this->display_footer();

        return implode("\n", $html);
    }

    function display_title()
    {
        $html = array();

        $html[] = '<div class="title"><div style="float: left;">' . Translation :: get('Alexia') . '</div>';
        $html[] = $this->display_actions();
        $html[] = '<div style="clear: both;"></div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    function get_conditions()
    {
        $conditions = array();

        $user = $this->get_user();
        $datamanager = AlexiaDataManager :: get_instance();

        if ($user->is_platform_admin())
        {
            $user_id = array();
            $groups = array();
        }
        else
        {
            $user_id = $user->get_id();
            $groups = $user->get_groups();
        }

        $subselect_condition = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Link :: get_type_name());
        $conditions[] = new SubselectCondition(AlexiaPublication :: PROPERTY_CONTENT_OBJECT, ContentObject :: PROPERTY_ID, ContentObject :: get_table_name(), $subselect_condition, null, RepositoryDataManager :: get_instance());

        $access = array();
        $access[] = new EqualityCondition(AlexiaPublication :: PROPERTY_PUBLISHER, $user_id = $user->get_id());
        $access[] = new InCondition(AlexiaPublicationUser :: PROPERTY_USER, $user_id, AlexiaPublicationUser :: get_table_name());
        $access[] = new InCondition(AlexiaPublicationGroup :: PROPERTY_GROUP_ID, $groups, AlexiaPublicationGroup :: get_table_name());
        if (! empty($user_id) || ! empty($groups))
        {
            $access[] = new AndCondition(array(new EqualityCondition(AlexiaPublicationUser :: PROPERTY_USER, null, AlexiaPublicationUser :: get_table_name()), new EqualityCondition(AlexiaPublicationGroup :: PROPERTY_GROUP_ID, null, AlexiaPublicationGroup :: get_table_name())));
        }
        $conditions[] = new OrCondition($access);

        if (! $user->is_platform_admin())
        {
            $visibility = array();
            $visibility[] = new EqualityCondition(AlexiaPublication :: PROPERTY_HIDDEN, false);
            $visibility[] = new EqualityCondition(AlexiaPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($visibility);

            $dates = array();
            $dates[] = new AndCondition(array(new InequalityCondition(AlexiaPublication :: PROPERTY_FROM_DATE, InequalityCondition :: GREATER_THAN_OR_EQUAL, time()), new InequalityCondition(AlexiaPublication :: PROPERTY_TO_DATE, InequalityCondition :: LESS_THAN_OR_EQUAL, time())));
            $dates[] = new EqualityCondition(AlexiaPublication :: PROPERTY_PUBLISHER, $user->get_id());
            $conditions[] = new OrCondition($dates);
        }

        return new AndCondition($conditions);
    }
}
?>
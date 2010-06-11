<?php
/**
 * $Id: publisher.class.php 205 2009-11-13 12:57:33Z vanpouckesven $
 * @package application.personal_calendar.personal_calendar_manager.component
 */
require_once dirname(__FILE__) . '/../../../phrases_manager.class.php';
require_once dirname(__FILE__) . '/../../../../publisher/phrases_publisher.class.php';

class PhrasesPublicationManagerPublisherComponent extends PhrasesPublicationManager
{

    /**
     * Runs this component and displays its output.
     */
    function run()
    {
        $trail = BreadcrumbTrail :: get_instance();
        $trail->add(new Breadcrumb($this->get_url(array(Application :: PARAM_ACTION => PhrasesManager :: ACTION_MANAGE_PHRASES)), Translation :: get('Phrases')));
        $trail->add(new Breadcrumb($this->get_url(), Translation :: get('Publish')));
        $trail->add_help('phrases general');

        $pub = new RepoViewer($this, array(Assessment :: get_type_name()));

        if (! $pub->is_ready_to_be_published())
        {
            $html[] = $pub->as_html();
        }
        else
        {
            $publisher = new PhrasesPublisher($pub);
            $html[] = $publisher->get_publications_form($pub->get_selected_objects());
        }

        $this->display_header($trail);
        //echo $publisher;
        echo implode("\n", $html);
        echo '<div style="clear: both;"></div>';
        $this->display_footer();
    }
}
?>
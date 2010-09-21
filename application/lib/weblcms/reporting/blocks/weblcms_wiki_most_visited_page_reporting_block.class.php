<?php
require_once dirname(__FILE__) . '/../weblcms_tool_reporting_block.class.php';
require_once PATH :: get_reporting_path() . '/lib/reporting_data.class.php';

class WeblcmsWikiMostVisitedPageReportingBlock extends WeblcmsToolReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('MostVisitedPage'), Translation :: get('NumberOfVisits')));

        $tdm = TrackingDataManager :: get_instance();

        $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($this->get_pid());
        $wiki = $publication->get_content_object();
        $complex_content_object_items = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $wiki->get_id(), ComplexContentObjectItem :: get_table_name()))->as_array();

        if (empty($complex_content_object_items))
        {
            return $reporting_data;
        }
        else
        {
            $most_visits = 0;
            $most_visited_page = null;

            foreach ($complex_content_object_items as $complex_content_object_item)
            {
                $conditions = array();
                $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*publication=' . $this->get_pid() . '*');
                $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*display_action=view_item*');
                $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*application=weblcms*');
                $conditions[] = new PatternMatchCondition(VisitTracker :: PROPERTY_LOCATION, '*selected_cloi=' . $complex_content_object_item->get_id() . '*');
                $condition = new AndCondition($conditions);

                $items = $tdm->retrieve_tracker_items('visit_tracker', 'VisitTracker', $condition);

                if (count($items) >= $most_visits)
                {
                    $most_visits = count($items);
                    $most_visited_page = $complex_content_object_item;
                }
            }
        }

        $url = 'run.php?go='.WeblcmsManager :: ACTION_VIEW_COURSE.'&course=' . $this->get_course_id() . '&tool=' . $this->get_tool() . '&application=weblcms&' . Tool :: PARAM_PUBLICATION_ID . '=' . $this->get_pid() . '&tool_action='.Tool :: ACTION_VIEW.'&display_action=view_item&selected_cloi=' . $most_visited_page->get_id();

        $reporting_data->add_category(0);
        $reporting_data->add_data_category_row(0, Translation :: get('MostVisitedPage'), '<a href="' . $url . '">' . $most_visited_page->get_ref_object()->get_title() . '</a>');
        $reporting_data->add_data_category_row(0, Translation :: get('NumberOfVisits'), $most_visits);
        $reporting_data->hide_categories();

        $reporting_data->hide_categories();

        return $reporting_data;
    }

    public function retrieve_data()
    {
        return $this->count_data();
    }

    public function get_available_displaymodes()
    {
        $modes = array();
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        return $modes;
    }
}

?>
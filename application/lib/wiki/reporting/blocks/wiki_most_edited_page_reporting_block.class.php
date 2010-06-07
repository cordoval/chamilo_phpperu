<?php
require_once dirname(__FILE__) . '/../wiki_reporting_block.class.php';
require_once PATH :: get_reporting_path() . '/lib/reporting_data.class.php';

class WikiMostEditedPageReportingBlock extends WikiReportingBlock
{

    public function count_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('MostEditedPage'), Translation :: get('NumberOfEdits')));

        $publication = WeblcmsDataManager :: get_instance()->retrieve_content_object_publication($this->get_pid());
        $wiki = $publication->get_content_object();
        $complex_content_object_items = RepositoryDataManager :: get_instance()->retrieve_complex_content_object_items(new EqualityCondition(ComplexContentObjectItem :: PROPERTY_PARENT, $wiki->get_id(), ComplexContentObjectItem :: get_table_name()))->as_array();

        if (empty($complex_content_object_items))
        {
            return $reporting_data;
        }
        else
        {
            $most_edits = 0;
            $most_edited_page = null;

            foreach ($complex_content_object_items as $complex_content_object_item)
            {
                $page_edits = RepositoryDataManager :: get_instance()->retrieve_content_object($complex_content_object_item->get_ref())->get_version_count();

                if ($page_edits >= $most_edits)
                {
                    $most_edits = $page_edits;
                    $most_edited_page = $complex_content_object_item;
                }
            }

            $url = 'run.php?go=courseviewer&course=' . $this->get_course_id() . '&tool=' . $this->get_tool() . '&application=weblcms&' . Tool :: PARAM_PUBLICATION_ID . '=' . $this->get_pid() . '&tool_action=view&display_action=view_item&selected_cloi=' . $most_edited_page->get_id();

            $reporting_data->add_category(0);
            $reporting_data->add_data_category_row(0, Translation :: get('MostEditedPage'), '<a href="' . $url . '">' . $most_edited_page->get_ref_object()->get_title() . '</a>');
            $reporting_data->add_data_category_row(0, Translation :: get('NumberOfEdits'), $most_edits);
            $reporting_data->hide_categories();
        }

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
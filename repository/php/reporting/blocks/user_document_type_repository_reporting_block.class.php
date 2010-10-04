<?php
require_once dirname(__FILE__) . '/../repository_reporting_block.class.php';
require_once dirname(__FILE__) . '/../../lib/content_object/document/document.class.php';

class UserDocumentTypeRepositoryReportingBlock extends RepositoryReportingBlock
{
    private $user_id;

    public function get_user_id()
    {
        return $this->user_id;
    }

    public function set_user_id($user_id)
    {
        $this->user_id = $user_id;
    }

    public function count_data()
    {
        return 0;
    }

    public function retrieve_data()
    {
        $reporting_data = new ReportingData();
        $reporting_data->set_rows(array(Translation :: get('Count')));

        $category_name = Translation :: get('Images');
        $reporting_data->add_category($category_name);
        $reporting_data->add_data_category_row($category_name, Translation :: get('Count'), $this->get_image_count());

        $category_name = Translation :: get('Video');
        $reporting_data->add_category($category_name);
        $reporting_data->add_data_category_row($category_name, Translation :: get('Count'), $this->get_video_count());

        $category_name = Translation :: get('Audio');
        $reporting_data->add_category($category_name);
        $reporting_data->add_data_category_row($category_name, Translation :: get('Count'), $this->get_audio_count());

        return $reporting_data;
    }

    function get_application()
    {
        return RepositoryManager :: APPLICATION_NAME;
    }

    public function get_available_displaymodes()
    {
        $modes = array();

        $modes[ReportingFormatter :: DISPLAY_TEXT] = Translation :: get('Text');
        $modes[ReportingFormatter :: DISPLAY_TABLE] = Translation :: get('Table');
        $modes[ReportingChartFormatter :: DISPLAY_BAR] = Translation :: get('Chart:Bar');
        $modes[ReportingChartFormatter :: DISPLAY_PIE] = Translation :: get('Chart:Pie');
        $modes[ReportingChartFormatter::DISPLAY_LINE] = Translation :: get('Chart:Line');
        $modes[ReportingChartFormatter::DISPLAY_FILLED_CUBIC] = Translation :: get('Chart:FilledCubic');

        return $modes;
    }

    public function get_image_count()
    {
        $rdm = RepositoryDataManager :: get_instance();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Document :: get_type_name());
        $type_conditions = array();

        $user_id = $this->get_user_id();
        if ($user_id)
        {
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id);
        }

        foreach (Document :: get_image_types() as $image_type)
        {
            $type_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $image_type, Document :: get_type_name());
        }

        $conditions[] = new OrCondition($type_conditions);
        $condition = new AndCondition($conditions);

        return $rdm->count_type_content_objects(Document :: get_type_name(), $condition);
    }

    public function get_video_count()
    {
        $rdm = RepositoryDataManager :: get_instance();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Document :: get_type_name());
        $type_conditions = array();

        $user_id = $this->get_user_id();
        if ($user_id)
        {
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id);
        }

        foreach (Document :: get_video_types() as $video_type)
        {
            $type_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $video_type, Document :: get_type_name());
        }

        $conditions[] = new OrCondition($type_conditions);
        $condition = new AndCondition($conditions);

        return $rdm->count_type_content_objects(Document :: get_type_name(), $condition);
    }

    public function get_audio_count()
    {
        $rdm = RepositoryDataManager :: get_instance();

        $conditions = array();
        $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_TYPE, Document :: get_type_name());
        $type_conditions = array();

        $user_id = $this->get_user_id();
        if ($user_id)
        {
            $conditions[] = new EqualityCondition(ContentObject :: PROPERTY_OWNER_ID, $user_id);
        }

        foreach (Document :: get_audio_types() as $audio_type)
        {
            $type_conditions[] = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.' . $audio_type, Document :: get_type_name());
        }

        $conditions[] = new OrCondition($type_conditions);
        $condition = new AndCondition($conditions);

        return $rdm->count_type_content_objects(Document :: get_type_name(), $condition);
    }
}
?>
<?php
class ReportingPdfExporter extends ReportingExporter
{
    function export()
    {
        $template = $this->get_template();
        $file = $this->get_file_name();
        $export = Export :: factory('pdf', $file);

        $data = str_replace(Path :: get(WEB_PATH), Path :: get(SYS_PATH), $template->export());
        $export->write_to_file_html($data);
    }
}
?>
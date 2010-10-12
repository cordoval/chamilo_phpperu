<?php
/**
 * $Id: hotpotatoes.class.php 200 2009-11-13 12:30:04Z kariboe $
 * @package repository.lib.content_object.hotpotatoes
 */
/**
 * This class represents an open question
 */
class Hotpotatoes extends ContentObject implements Versionable
{
    const PROPERTY_PATH = 'path';
    const PROPERTY_MAXIMUM_ATTEMPTS = 'max_attempts';

	const CLASS_NAME = __CLASS__;

	static function get_type_name()
	{
		return Utilities :: camelcase_to_underscores(self :: CLASS_NAME);
	}

    static function get_additional_property_names()
    {
        return array(self :: PROPERTY_PATH, self :: PROPERTY_MAXIMUM_ATTEMPTS);
    }

    const TYPE_HOTPOTATOES = 3;

    function get_assessment_type()
    {
        return self :: TYPE_HOTPOTATOES;
    }

    function get_times_taken()
    {
        return WeblcmsDataManager :: get_instance()->get_num_user_assessments($this);
    }

    function get_average_score()
    {
        return WeblcmsDataManager :: get_instance()->get_average_score($this);
    }

    function get_maximum_score()
    {
        //return WeblcmsDataManager :: get_instance()->get_maximum_score($this);
        return 100;
    }

    function get_maximum_attempts()
    {
        return $this->get_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS);
    }

    function set_maximum_attempts($value)
    {
        $this->set_additional_property(self :: PROPERTY_MAXIMUM_ATTEMPTS, $value);
    }

    function get_path()
    {
        return $this->get_additional_property(self :: PROPERTY_PATH);
    }

    function set_path($path)
    {
        return $this->set_additional_property(self :: PROPERTY_PATH, $path);
    }

    function get_full_path()
    {
        return Path :: get(SYS_HOTPOTATOES_PATH) . $this->get_owner_id() . '/' . $this->get_path();
    }

    function get_full_url()
    {
        return Path :: get(WEB_HOTPOTATOES_PATH) . $this->get_owner_id() . '/' . $this->get_path();
    }

    function delete()
    {
        $this->delete_file();
        parent :: delete();
    }

    function delete_file()
    {
        $dir = dirname($this->get_full_path());
        Filesystem :: remove($dir);
    }

    function add_javascript($postback_url, $goback_url, $tracker_id)
    {
        $content = $this->read_file_content();
        $js_content = $this->replace_javascript($content, $postback_url, $goback_url, $tracker_id);
        $path = $this->write_file_content($js_content);

        return $path;
    }

    private function read_file_content()
    {
        $full_file_path = $this->get_full_path();

        if (is_file($full_file_path))
        {
            if (! ($fp = fopen(urldecode($full_file_path), "r")))
            {
                return "";
            }
            $contents = fread($fp, filesize($full_file_path));
            fclose($fp);
            return $contents;
        }
    }

    private function write_file_content($content)
    {
        $full_file_path = $this->get_full_path() . '.t.htm';
        $full_web_path = $this->get_full_url() . '.t.htm';
        Filesystem :: remove($full_file_path);

        if (($fp = fopen(urldecode($full_file_path), "w")))
        {
            fwrite($fp, $content);
            fclose($fp);
        }

        return $full_web_path;
    }

    private function replace_javascript($content, $postback_url, $goback_url, $tracker_id)
    {
        $mit = "function Finish(){";
        $js_content = "var SaveScoreVariable = 0; // This variable included by Chamilo System\n" . "function mySaveScore() // This function included by Chamilo System\n" . "{\n" . "   if (SaveScoreVariable==0)\n" . "		{\n" . "			SaveScoreVariable = 1;\n" . "			var result=jQuery.ajax({type: 'POST', url:'" . $postback_url . "', data: {id: " . $tracker_id . ", score: Score}, async: false}).responseText;\n";
        //"			alert(result);";


        if ($goback_url)
        {
            $js_content .= "		if (C.ie)\n" . "			{\n" . //	"				window.alert(Score);\n".
            "				document.parent.location.href=\"" . $goback_url . "\"\n" . "			}\n" . "			else\n" . "			{\n" . //	"				window.alert(Score);\n".
            "				window.parent.location.href=\"" . $goback_url . "\"\n" . "			}\n";
        }

        $js_content .= "		}\n" . " }\n" .

        "// Must be included \n" . "function Finish(){\n" . " mySaveScore();";
        $newcontent = str_replace($mit, $js_content, $content);
        $prehref = "<!-- BeginTopNavButtons -->";
        $posthref = "<!-- BeginTopNavButtons --><!-- edited by Chamilo -->";
        $newcontent = str_replace($prehref, $posthref, $newcontent);

        $jquery_content = "<head>\n<script src='" . Path :: get(WEB_PATH) . "plugin/jquery/jquery.min.js' type='text/javascript'></script>";
        $add_to = '<head>';
        $newcontent = str_replace($add_to, $jquery_content, $newcontent);

        return $newcontent;
    }
}
?>
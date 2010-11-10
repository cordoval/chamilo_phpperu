<?php
namespace application\metadata;
use common\libraries\Translation;
use common\libraries\Request;
use common\libraries\Utilities;

/**
 * Component to delete metadata_default_values objects
 * @author Sven Vanpoucke
 * @author Jens Vanderheyden
 */
class MetadataManagerMetadataDefaultValueImporterComponent extends MetadataManager
{
    /**
     * Runs this component and displays its output.
     */
    function run()
    {
       if(!$property_type_id = Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE))
       {
           exit(Translation :: get('NoObjectSelected', array('OBJECT' => Translation :: get('MetadataPropertyType')), Utilities :: COMMON_LIBRARIES));
       }

       //$metadata_property_type = $this->retrieve_metadata_property_type($property_type_id);

       $form = new UploadForm($this->get_url(array(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE => Request :: get(MetadataManager :: PARAM_METADATA_PROPERTY_TYPE))));
       
       if($form->validate())
       {
           if(isset($_FILES['File']))
           {
            if(is_uploaded_file($_FILES['File']['tmp_name']))
            {
                $handle = fopen($_FILES['File']['tmp_name'], 'r');
                $out = '<h3>' . Translation :: get('Import', null, Utilities :: COMMON_LIBRARIES) . '</h3>';
                while(($data = fgetcsv($handle, 1000, ";")) !== FALSE)
                {
                    $num = count($data);
                    
                    $metadata_default_value = new MetadataDefaultValue();
                    $metadata_default_value->set_property_type_id($property_type_id);
                    $metadata_default_value->set_value($data[0]);
                    //if(isset($data[1])) $metadata_default_value->set_property_attribute_type_id ($data[1]);

                    if($metadata_default_value->create())
                    {
                        $out .=  '<p>' . Translation :: get('ObjectCreated', array('OBJECT' => Translation :: get('MetadataDefaultValue')), Utilities :: COMMON_LIBRARIES) . ' ' . $data[0]  . '</p>' . "\n";
                    }
                    else
                    {
                        $out .=  '<p>' . Translation :: get('ObjectNotCreated', array('OBJECT' => Translation :: get('MetadataDefaultValue')), Utilities :: COMMON_LIBRARIES) . ' ' . $data[0] . '</p>' . "\n";
                    }

                }
                fclose($handle);
            }
            else
            {
                $out = Translation :: get('NoFileUploaded',null, Utilities :: COMMON_LIBRARIES);
            }
            
           }
           else
           {
               $out = Translation :: get('NoFileUploaded', null, Utilities :: COMMON_LIBRARIES);
           }
           $this->display_header();
            echo $out;
            $this->display_footer();
       }
       else
       {
           $this->display_header();
           $form->display();
           $this->display_footer();
       }
       
    }
}
?>
<?php

class ImsMetadataReader extends  ImsXmlReader
{
	/**
	 * 
	 * @param unknown_type $schema
	 * @param unknown_type $schemaversion
	 * @param unknown_type $item
	 * @return ImsLomReader|NULL
	 */
	public static function factory($schema, $schemaversion, $item=''){
		if($schema==ImsLomReader::is_compatible($schema, $schemaversion)){
			return new ImsLomReader($item);
		}else{
			return null;
		}
	}

    function __construct($item = '')
    {
    	parent::__construct($item);
    }
    
    public function to_metadata_object(){
    	return null;
    }
    
}
?>
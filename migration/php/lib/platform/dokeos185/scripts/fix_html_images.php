<?php

require_once dirname(__FILE__) . '/../../../../../../common/global.inc.php';

use common\libraries\PatternMatchCondition;
use repository\content_object\document\Document;
use repository\RepositoryDataManager;
use migration\Dokeos185TextFieldParser;

$condition = new PatternMatchCondition(Document :: PROPERTY_FILENAME, '*.htm*', Document :: get_type_name());
$documents = RepositoryDataManager :: get_instance()->retrieve_type_content_objects(Document :: get_type_name(), $condition);

$parser = Dokeos185TextFieldParser :: factory();

while($document = $documents->next_result())
{
    $file = $document->get_full_path();
    dump($file);
    $contents = file_get_contents($file);
    $contents = $parser->parse($contents);
    file_put_contents($file, $contents);
}

?>

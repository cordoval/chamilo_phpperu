<?php

namespace common\libraries\application_generator;
use common\libraries\Path;

/**
 * Settings for dataclass generator
 */
//$application['location'] = Path :: get(SYS_PATH) . 'common/libraries/php/util/application_generator/examples/linker/';
//$application['name'] = 'linker';

$application['location'] = Path :: get(SYS_PATH) . 'application/test/';
$application['name'] = 'test';

$application['author'] = '';
$application['options']['link']['table'] = 1;
?>
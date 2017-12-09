<?php

namespace Game\Model\Lib\Unit\Need;

use Ap\Model\Row as Prototype;

use Game\Model\Lib\Unit\Provider AS UnitProvider;
use Game\Model\Lib\Resource\Provider AS ResourceProvider;
use Game\Model\Lib\Resource\ValueProvider;

class Row extends Prototype
{
    use UnitProvider,
        ResourceProvider{
        ResourceProvider::getSm insteadof UnitProvider;
        ResourceProvider::setSm insteadof UnitProvider;
    }
    
    use ValueProvider;
}
<?php

namespace Game\Model\Lib\Ship\Need;

use Ap\Model\Row as Prototype;

use Game\Model\Lib\Ship\Provider AS ShipProvider;
use Game\Model\Lib\Resource\Provider AS ResourceProvider;
use Game\Model\Lib\Resource\ValueProvider;

class Row extends Prototype
{
    use ShipProvider,
        ResourceProvider{
        ResourceProvider::getSm insteadof ShipProvider;
        ResourceProvider::setSm insteadof ShipProvider;
    }
    use ValueProvider;
}
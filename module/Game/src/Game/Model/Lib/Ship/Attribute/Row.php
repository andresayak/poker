<?php

namespace Game\Model\Lib\Ship\Attribute;

use Ap\Model\Row as Prototype;

use Game\Model\Lib\Ship\Provider AS ShipProvider;
use Game\Model\Lib\Attribute\Provider AS AttributeProvider;
use Game\Model\Lib\Attribute\ValueProvider;
class Row extends Prototype
{
    use ShipProvider,
        AttributeProvider{
        AttributeProvider::getSm insteadof ShipProvider;
        AttributeProvider::setSm insteadof ShipProvider;
    }
    use ValueProvider;
}
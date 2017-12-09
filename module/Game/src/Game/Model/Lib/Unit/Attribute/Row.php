<?php

namespace Game\Model\Lib\Unit\Attribute;

use Ap\Model\Row as Prototype;
use Game\Model\Lib\Unit\Provider AS UnitProvider;
use Game\Model\Lib\Attribute\Provider AS AttributeProvider;
use Game\Model\Lib\Attribute\ValueProvider;

class Row extends Prototype
{
    use UnitProvider,
        AttributeProvider{
        AttributeProvider::getSm insteadof UnitProvider;
        AttributeProvider::setSm insteadof UnitProvider;
    }
    use ValueProvider;
}
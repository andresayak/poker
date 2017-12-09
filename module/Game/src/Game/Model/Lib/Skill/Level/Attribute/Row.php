<?php

namespace Game\Model\Lib\Skill\Level\Attribute;

use Ap\Model\Row as Prototype;
use Game\Model\Lib\Skill\Level\Provider AS LevelProvider;
use Game\Model\Lib\Attribute\Provider AS AttributeProvider;
use Game\Model\Lib\Attribute\ValueProvider;

class Row extends Prototype
{
    use LevelProvider,
        AttributeProvider{
        AttributeProvider::getSm insteadof LevelProvider;
        AttributeProvider::setSm insteadof LevelProvider;
    }
    use ValueProvider;
}
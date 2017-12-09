<?php

namespace Game\Model\Lib\Building\Level\Need;

use Ap\Model\Row as Prototype;

use Game\Model\Lib\Building\Level\Provider AS LevelProvider;
use Game\Model\Lib\Resource\Provider AS ResourceProvider;
use Game\Model\Lib\Resource\ValueProvider;

class Row extends Prototype
{
    use LevelProvider,
        ResourceProvider{
        ResourceProvider::getSm insteadof LevelProvider;
        ResourceProvider::setSm insteadof LevelProvider;
    }
    
    use ValueProvider;
}
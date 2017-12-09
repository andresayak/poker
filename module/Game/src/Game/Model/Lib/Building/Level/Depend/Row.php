<?php

namespace Game\Model\Lib\Building\Level\Depend;

use Ap\Model\Row as Prototype;
use Game\Model\Lib\Building\Level\Provider AS LevelProvider;
use Game\Provider\Depend AS DependProvider;

class Row extends Prototype
{
    use LevelProvider, DependProvider;
}
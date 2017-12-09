<?php

namespace Game\Model\Lib\Unit\Depend;

use Ap\Model\Row as Prototype;
use Game\Model\Lib\Unit\Provider AS UnitProvider;
use Game\Provider\Depend AS DependProvider;

class Row extends Prototype
{
    use UnitProvider, DependProvider;
}
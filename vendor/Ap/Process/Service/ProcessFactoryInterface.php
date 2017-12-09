<?php

namespace Ap\Process\Service;

use Ap\Process\ProcessPool;

interface ProcessFactoryInterface
{
    /**
     *
     * @param string $name имя процесса
     * @param ProcessPool $pool
     * @param array $options опции для создаваемого процесса
     */
    public function getProcess($name, ProcessPool $pool, array $options = array());
}
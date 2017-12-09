<?php

namespace Ap\Process;

class LockFile
{
    /**
     * lock-файл
     *
     * @var string
     */
    protected $lockFile;

    /**
     *
     * @var mixed
     */
    protected $handle = null;

    /**
     *
     * @param string $fileName
     */
    public function __construct($fileName = null)
    {
        if (null !== $fileName) {
            $this->lockFile = $fileName;
        }
    }

    /**
     *
     * @return boolean
     */
    public function take()
    {
        return $this->lock();
    }

    /**
     *
     * @return boolean
     */
    public function release()
    {
        return $this->unlock();
    }

    /**
     * 'Захватывает' lock-файл. Возвращает false если другая копия уже выполняется
     *
     * @return boolean
     */
    protected function lock()
    {
        return true;
        $handle = $this->getHandle(true);
        if (flock($handle, LOCK_EX | LOCK_NB)) {
            fwrite($handle, getmypid());
            fflush($handle);
            return true;
        } else {
            $this->closeHandle();
        }

        return false;
    }

    /**
     *
     * @return mixed
     */
    protected function getHandle($force = false)
    {
        if (null === $this->handle && $force) {
            $this->handle = fopen($this->lockFile, 'w+');
        }

        return $this->handle;
    }

    /**
     * @return void
     */
    protected function closeHandle()
    {
        if (null === $this->handle) {
            throw new \Exception('handle is null');
        }

        fclose($this->handle);
        $this->handle = null;
    }

    /**
     * 'Освобождает' lock-файл
     *
     * @return boolean
     */
    protected function unlock()
    {
        $handle = $this->getHandle();
        if (null !== $handle) {
            flock($handle, LOCK_UN);

            $this->closeHandle();
            unlink($this->lockFile);
        }

        return true;
    }

    /**
     * 
     */
    public function __destruct()
    {
        $this->release();
    }
}
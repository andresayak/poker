<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Ap\Model;

class HydratorObjectProperty extends \Zend\Stdlib\Hydrator\ObjectProperty
{
    use \Ap\Provider\ProvidesServiceManager;
    public function extract($object)
    {
        $data = $object->toArray();
        $filter = $this->getFilter();
        foreach ($data as $name => $value) {
            // Filter keys, removing any we don't want
            if (! $filter->filter($name)) {
                unset($data[$name]);
                continue;
            }

            // Replace name if extracted differ
            $extracted = $this->extractName($name, $object);

            if ($extracted !== $name) {
                unset($data[$name]);
                $name = $extracted;
            }

            $data[$name] = $this->extractValue($name, $value, $object);
        }

        return $data;
    }

    public function hydrate(array $data, $object)
    {
        foreach ($object->getTable()->getCols() as $col){
            $property = $this->hydrateName($col, $data);
            $object->$property = $this->hydrateValue($property, (isset($data[$col])?$data[$col]:$object->$col), $data);
        }

        return $object;
    }
}

<?php

namespace App\Entity;

class ParentEntity
{
    /**
     * Assign entity properties using an array
     * 
     * @param array $attributes assoc array of values to assign
     * @return null 
     */
    public function fromArray(array $attributes)
    {
        foreach ($attributes as $name => $value) {
            if (property_exists($this, $name)) {
                $methodName = $this->_getSetterName($name);
                if ($methodName) {
                    $this->{$methodName}($value);
                } else {
                    $this->$name = $value;
                }
            }
        }
    }

    /**
     * Get property setter method name (if exists)
     * 
     * @param string $propertyName entity property name
     * @return false|string 
     */
    protected function _getSetterName($propertyName)
    {
        $prefixes = array('add', 'set');

        foreach ($prefixes as $prefix) {
            $methodName = sprintf('%s%s', $prefix, ucfirst(strtolower(str_replace("_", "", $propertyName))));

            if (method_exists($this, $methodName)) {
                return $methodName;
            }
        }
        return false;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: williamdelrosario
 * Date: 3/9/18
 * Time: 10:51 AM
 */

namespace AppBundle\Entity;


class EntityMerger
{
    /**
     * @param $entity
     * @param $changes
     */
    public function merge($entity, $changes): void
    {
        $entityClassName = get_class($entity);
//        dump($entityClassName);die;
        if (false === $entityClassName) {
            throw new \InvalidArgumentException('$entity is not a class');
        }

        // Get $changes class name or false if it's not a class
        $changesClassName = get_class($changes);

        if (false === $changesClassName) {
            throw new \InvalidArgumentException('$changes is not a class');
        }

        // Continue only if $changes object is of the same class as $entity or $changes is a subclass of $entity
        if (!is_a($changes, $entityClassName)) {
            throw new \InvalidArgumentException
            (
                "Cannot merge object of class $changesClassName with object of class $entityClassName"
            );
        }
    }
}
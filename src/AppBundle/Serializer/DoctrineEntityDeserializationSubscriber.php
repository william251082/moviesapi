<?php
/**
 * Created by PhpStorm.
 * User: williamdelrosario
 * Date: 3/6/18
 * Time: 8:24 PM
 */

namespace AppBundle\Serializer;

use AppBundle\Annotation\DeserializeEntity;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DoctrineEntityDeserializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;
    /**
     * @var Registry
     */
    private $doctrineRegistry;

    public function  __construct(AnnotationReader $annotationReader, Registry $doctrineRegistry)
    {
        $this->annotationReader = $annotationReader;
        $this->doctrineRegistry = $doctrineRegistry;
    }

    public static function getSubscribedEvents()
    {
        return
        [
            [
                'event' => 'serializer.pre_deserialize',
                'method' => 'onPreDeserialize',
                'format' => 'json'
            ],
            [
                'event' => 'serializer.post_deserialize',
                'method' => 'onPostDeserialize',
                'format' => 'json'
            ]
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
//       dump($event->getType()['name'], $event->getData()); die;
//        dump($event->getData());
        $deserializedType = $event->getType()['name'];

        if (!class_exists($deserializedType))
        {
            return;
        }
            $data = $event->getData();
            $class = new \ReflectionClass($deserializedType);
//                            dump($class);die;
            foreach ($class->getProperties() as $property) {
                if (!isset($data[$property->name])) {
                    continue;
                }
                /** @var DeserializeEntity $annotation */
                $annotation = $this->annotationReader->getPropertyAnnotation
                (
                    $property,
                    DeserializeEntity::class
                );
//                dump($annotation);
                if (null === $annotation || !class_exists($annotation->type)) {
                    continue;
                }

                $data[$property->name] =
                    [
                        $annotation->idField => $data[$property->name]
                    ];

//                dump($data);die;
                $event->setData($data);
            }
//            die;
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
//        dump($event); die;
        $deserializedType = $event->getType()['name'];

        if (!class_exists($deserializedType))
        {
            return;
        }

        $object = $event->getObject();
        $reflection = new \ReflectionObject($object);

//        dump($object);die;
//        dump($reflection);die;

        foreach ($reflection->getProperties() as $property)
        {
            /** @var DeserializeEntity $annotation */
            $annotation = $this->annotationReader->getPropertyAnnotation
            (
                $property,
                DeserializeEntity::class
            );

            if (null === $annotation || !class_exists($annotation->type))
            {
                continue;
            }
//            dump($reflection);die;
//            dump($annotation);die;
            if (!$reflection->hasMethod($annotation->setter))
            {
                throw new \LogicException
                (
                    "Object {$reflection->getName()} does not have the {$annotation->setter} method"
                );
            }
//                        dump($reflection);die;
            $property->setAccessible(true);
            $deserializedEntity = $property->getValue($object);
//            dump($deserializedEntity);die;

            if (null ===$deserializedEntity)
            {
                return;
            }
            $entityId = $deserializedEntity->{$annotation->idGetter}();
//            dump($deserializedEntity, $entityId);die;
            $repository = $this->doctrineRegistry->getRepository($annotation->type);
//            dump($repository);die;
            $entity = $repository->find($entityId);
//            dump($entity);die;

            if (null ===$entity)
            {
                throw new NotFoundHttpException
                (
                    "Resource {$reflection->getShortName()}/$entityId"
                );
            }
            $object->{$annotation->setter}($entity);
        }
    }
}
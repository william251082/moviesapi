<?php
/**
 * Created by PhpStorm.
 * User: williamdelrosario
 * Date: 3/6/18
 * Time: 8:24 PM
 */

namespace AppBundle\Serializer;

use AppBundle\Annotation\DeserializeEntity;
use Doctrine\Common\Annotations\AnnotationReader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;

class DoctrineEntityDeserializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function  __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
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
//        dump($event->getData()); die;
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

//                dump($data);
                $event->setData($data);
            }
//            die;
    }

    public function onPostDeserialize(ObjectEvent $event)
    {
        dump($event); die;
    }
}
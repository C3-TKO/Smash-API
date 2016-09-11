<?php

namespace AppBundle\Serializer;


use AppBundle\AppBundle;
use Doctrine\Common\Annotations\Reader;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\RouterInterface;
use AppBundle\Entity\Player;
use AppBundle\Annotation\Link;

class LinkSerializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * @var ExpressionLanguage
     */
    private $expressionLanguage;

    /**
     * LinkSerializationSubscriber constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router, Reader $annotationReader)
    {
        $this->router = $router;
        $this->annotationReader = $annotationReader;
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /**
         * @var JsonSerializationVisitor $visitor
         */
        $visitor = $event->getVisitor();
        $object = $event->getObject();
        $annotations = $this->annotationReader
            ->getClassAnnotations(new \ReflectionObject($object));
        $links = array();

        foreach ($annotations as $annotation) {
            if ($annotation instanceof Link) {
                if ($annotation->url) {
                    $uri = $this->evaluate($annotation->url, $object);
                } else {
                    $uri = $this->router->generate(
                        $annotation->route,
                        $this->resolveParams($annotation->params, $object)
                    );
                }
                // allow a blank URI to be an optional link
                if ($uri) {
                    $links[$annotation->name] = $uri;
                }
            }
        }
        if ($links) {
            $visitor->addData('_links', $links);
        }
    }

    private function resolveParams(array $params, $object)
    {
        foreach ($params as $key => $param) {
            $params[$key] = $this->evaluate($param, $object);
        }
        return $params;
    }

    private function evaluate($expression, $object)
    {
        return $this->expressionLanguage
            ->evaluate($expression, array('object' => $object));
    }

    /**
     * Returns the events to which this class has subscribed.
     *
     * Return format:
     *     array(
     *         array('event' => 'the-event-name', 'method' => 'onEventName', 'class' => 'some-class', 'format' => 'json'),
     *         array(...),
     *     )
     *
     * The class may be omitted if the class wants to subscribe to events of all classes.
     * Same goes for the format key.
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            array(
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'format' => 'json',
            )
        );
    }
}
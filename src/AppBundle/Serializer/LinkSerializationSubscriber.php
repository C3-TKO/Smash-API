<?php

namespace AppBundle\Serializer;


use AppBundle\AppBundle;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Symfony\Component\Routing\RouterInterface;
use AppBundle\Entity\Player;

class LinkSerializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * LinkSerializationSubscriber constructor.
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
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
        return [
            [
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'format' => 'json',
                'class' => 'AppBundle\Entity\Player'
            ]
        ];
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        /**
         * @var JsonSerializationVisitor $visitor
         */
        $visitor = $event->getVisitor();
        /**
         * @var AppBundle\Entity\Player $player
         */
        $player = $event->getObject();
        $visitor->addData(
            'uri',
            $this->router->generate('get_player', [
                'id' => $player->getId()
            ])
        );
    }
}
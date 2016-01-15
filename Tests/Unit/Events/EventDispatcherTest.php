<?php


namespace Smartbox\Integration\FrameworkBundle\Tests\Unit\Events;

use Smartbox\Integration\FrameworkBundle\Events\HandlerEvent;
use Smartbox\Integration\FrameworkBundle\Drivers\Queue\ArrayQueueDriver;
use Smartbox\Integration\FrameworkBundle\Events\EventDispatcher;
use Smartbox\Integration\FrameworkBundle\Events\EventFilterInterface;
use Smartbox\Integration\FrameworkBundle\Events\EventFiltersRegistry;
use Smartbox\Integration\FrameworkBundle\Messages\EventMessage;
use Smartbox\Integration\FrameworkBundle\Messages\Queues\QueueMessage;
use Symfony\Component\DependencyInjection\Container;

class EventDispatcherTest extends \PHPUnit_Framework_TestCase{

    public function testShouldDeferEvent(){
        $filterPass = $this->getMock(EventFilterInterface::class);
        $filterPass->method('filter')->willReturn(true);

        $filtersRegistry = new EventFiltersRegistry();
        $filtersRegistry->addDeferringFilter($filterPass);

        $queueDriver = new ArrayQueueDriver();

        $event = new HandlerEvent();

        $container = new Container();
        $container->set('smartesb.registry.event_filters',$filtersRegistry);
        $container->set('smartesb.drivers.queue.events',$queueDriver);

        $container->setParameter('smartesb.events_queue_name', 'test_queue');

        $dispatcher = new EventDispatcher($container);
        $dispatcher->dispatch("test_event", $event);

        $messages = $queueDriver->getArrayForQueue('test_queue');

        $this->assertCount(1,$messages);
        /** @var QueueMessage $message */
        $message = $messages[0];

        $this->assertInstanceOf(QueueMessage::class,$message);

        $this->assertInstanceOf(EventMessage::class,$message->getBody());

        $this->assertEquals($message->getBody()->getBody(),$event);
    }


    public function testShouldNotDeferEventIfDeferred(){
        $filterPass = $this->getMock(EventFilterInterface::class);
        $filterPass->method('filter')->willReturn(true);

        $filtersRegistry = new EventFiltersRegistry();
        $filtersRegistry->addDeferringFilter($filterPass);

        $queueDriver = new ArrayQueueDriver();

        $event = new HandlerEvent();

        $container = new Container();
        $container->set('smartesb.registry.event_filters',$filtersRegistry);
        $container->set('smartesb.drivers.queue.events',$queueDriver);

        $container->setParameter('smartesb.events_queue_name', 'test_queue');

        $dispatcher = new EventDispatcher($container);
        $dispatcher->dispatch("test_event.deferred", $event);

        $messages = $queueDriver->getArrayForQueue('test_queue');

        $this->assertCount(0,$messages);
    }


    public function testShouldNotDeferEventIfDoesNotPassFilter(){
        $filterDoesNotPass = $this->getMock(EventFilterInterface::class);
        $filterDoesNotPass->method('filter')->willReturn(false);

        $filtersRegistry = new EventFiltersRegistry();
        $filtersRegistry->addDeferringFilter($filterDoesNotPass);

        $queueDriver = new ArrayQueueDriver();

        $event = new HandlerEvent();

        $container = new Container();
        $container->set('smartesb.registry.event_filters',$filtersRegistry);
        $container->set('smartesb.drivers.queue.events',$queueDriver);

        $container->setParameter('smartesb.events_queue_name', 'test_queue');

        $dispatcher = new EventDispatcher($container);
        $dispatcher->dispatch("test_event.deferred", $event);

        $messages = $queueDriver->getArrayForQueue('test_queue');

        $this->assertCount(0,$messages);
    }

    public function tearDown(){
        parent::tearDown();
        ArrayQueueDriver::$array = array();
    }

}
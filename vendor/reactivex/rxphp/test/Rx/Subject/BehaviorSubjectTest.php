<?php

declare(strict_types = 1);

namespace Rx\Subject;

use Exception;
use Rx\TestCase;

class BehaviorSubjectTest extends TestCase
{
    /**
     * @test
     */
    public function it_throws_when_subscribing_to_a_disposed_subject()
    {
        $this->expectException(\RuntimeException::class);
        $subject = new BehaviorSubject();
        $subject->dispose();

        $observer = $this->createMock('Rx\ObserverInterface');
        $subject->subscribe($observer);
    }

    /**
     * @test
     */
    public function it_exposes_if_it_has_observers()
    {
        $subject = new BehaviorSubject();

        $this->assertFalse($subject->hasObservers());

        $observer = $this->createMock('Rx\ObserverInterface');
        $subject->subscribe($observer);
        $this->assertTrue($subject->hasObservers());
    }

    /**
     * @test
     */
    public function it_exposes_if_it_is_disposed()
    {
        $subject = new BehaviorSubject();

        $this->assertFalse($subject->isDisposed());

        $subject->dispose();
        $this->assertTrue($subject->isDisposed());
    }

    /**
     * @test
     */
    public function it_has_no_observers_after_disposing()
    {
        $subject = new BehaviorSubject();

        $observer = $this->createMock('Rx\ObserverInterface');
        $subject->subscribe($observer);
        $this->assertTrue($subject->hasObservers());

        $subject->dispose();
        $this->assertFalse($subject->hasObservers());
    }

    /**
     * @test
     */
    public function it_returns_true_if_an_observer_is_removed()
    {
        $subject = new BehaviorSubject();

        $observer = $this->createMock('Rx\ObserverInterface');
        $subject->subscribe($observer);
        $this->assertTrue($subject->hasObservers());

        $this->assertTrue($subject->removeObserver($observer));
        $this->assertFalse($subject->hasObservers());
    }

    /**
     * @test
     */
    public function it_returns_false_if_an_observer_is_not_subscribed()
    {
        $subject = new BehaviorSubject();

        $observer = $this->createMock('Rx\ObserverInterface');

        $this->assertFalse($subject->removeObserver($observer));
        $this->assertFalse($subject->hasObservers());
    }

    /**
     * @test
     */
    public function it_passes_exception_on_subscribe_if_already_stopped()
    {
        $exception = new Exception('fail');
        $subject   = new BehaviorSubject();
        $subject->onError($exception);

        $observer = $this->createMock('Rx\ObserverInterface');
        $observer->expects($this->once())
            ->method('onError')
            ->with($this->equalTo($exception));

        $subject->subscribe($observer);
    }

    /**
     * @test
     */
    public function it_passes_on_complete_on_subscribe_if_already_stopped()
    {
        $subject   = new BehaviorSubject();
        $subject->onCompleted();

        $observer = $this->createMock('Rx\ObserverInterface');
        $observer->expects($this->once())
            ->method('onCompleted');

        $subject->subscribe($observer);
    }

    /**
     * @test
     */
    public function it_passes_on_error_if_not_disposed()
    {
        $exception = new Exception('fail');
        $subject   = new BehaviorSubject();

        $observer = $this->createMock('Rx\ObserverInterface');
        $observer->expects($this->once())
            ->method('onError')
            ->with($this->equalTo($exception));


        $subject->subscribe($observer);
        $subject->onError($exception);
    }

    /**
     * @test
     */
    public function it_passes_on_complete_if_not_disposed()
    {
        $subject  = new BehaviorSubject();

        $observer = $this->createMock('Rx\ObserverInterface');
        $observer->expects($this->once())
            ->method('onCompleted');

        $subject->subscribe($observer);
        $subject->onCompleted();
    }

    /**
     * @test
     */
    public function it_passes_on_next_if_not_disposed()
    {
        $subject  = new BehaviorSubject();
        $value    = 42;

        $observer = $this->createMock('Rx\ObserverInterface');

        $observer->expects($this->exactly(2))
            ->method('onNext')
            ->will($this->returnValueMap(array(
                array(null),
                array($value)
            ))
        );

        $subject->subscribe($observer);
        $subject->onNext($value);
    }

    /**
     * @test
     */
    public function it_passes_on_next_if_not_disposed_with_init()
    {
        $value    = 42;
        $subject  = new BehaviorSubject($value);

        $observer = $this->createMock('Rx\ObserverInterface');

        $observer->expects($this->once())
            ->method('onNext')
            ->with($this->equalTo($value));

        $subject->subscribe($observer);
    }

    /**
     * @test
     */
    public function it_does_not_pass_if_already_stopped()
    {
        $subject  = new BehaviorSubject();

        $observer = $this->createMock('Rx\ObserverInterface');
        $observer->expects($this->once())
            ->method('onCompleted');

        $observer->expects($this->once())
            ->method('onNext');

        $observer->expects($this->never())
            ->method('onError');

        $subject->subscribe($observer);
        $subject->onCompleted();

        $subject->onError(new Exception('fail'));
        $subject->onNext(42);
        $subject->onCompleted();
    }

    /**
     * @test
     */
    public function it_throws_on_error_if_disposed()
    {
        $this->expectException(\RuntimeException::class);
        $subject   = new BehaviorSubject();

        $subject->dispose();
        $subject->onError(new Exception('fail'));
    }

    /**
     * @test
     */
    public function it_passes_on_complete_if_disposed()
    {
        $this->expectException(\RuntimeException::class);
        $subject  = new BehaviorSubject();

        $subject->dispose();
        $subject->onCompleted();
    }

    /**
     * @test
     */
    public function it_passes_on_next_if_disposed()
    {
        $this->expectException(\RuntimeException::class);
        $subject  = new BehaviorSubject();
        $value    = 42;

        $subject->dispose();
        $subject->onNext($value);
    }
}

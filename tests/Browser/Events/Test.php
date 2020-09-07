<?php

namespace Tests\Browser\Events;

use Livewire\Livewire;
use Tests\Browser\TestCase;

class Test extends TestCase
{
    public function test()
    {
        $this->browse(function ($browser) {
            Livewire::visit($browser, Component::class)
                /**
                 * receive event from global fire
                 */
                ->waitForLivewire()->tap(function ($browser) { $browser->script('window.livewire.emit("foo", "bar")'); })
                ->pause(350)
                ->assertSeeIn('@lastEventForParent', 'bar')
                ->assertSeeIn('@lastEventForChildA', 'bar')
                ->assertSeeIn('@lastEventForChildB', 'bar')

                /**
                 * receive event from action fire
                 */
                ->waitForLivewire()->click('@emit.baz')
                ->pause(350)
                ->assertSeeIn('@lastEventForParent', 'baz')
                ->assertSeeIn('@lastEventForChildA', 'baz')
                ->assertSeeIn('@lastEventForChildB', 'baz')

                /**
                 * receive event from component fire, and make sure global listener receives event too
                 */
                ->tap(function ($b) { $b->script([
                    "window.lastFooEventValue = ''",
                    "window.livewire.on('foo', value => { lastFooEventValue = value })",
                ]);})
                ->waitForLivewire()->click('@emit.bob')
                ->pause(350)
                ->tap(function ($b) {
                    $this->assertEquals(['bob'], $b->script('return window.lastFooEventValue'));
                })

                /**
                 * receive event from component fired only to ancestors, and make sure global listener doesnt receive it
                 */
                ->waitForLivewire()->click('@emit.lob')
                ->pause(350)
                ->assertSeeIn('@lastEventForParent', 'lob')
                ->assertSeeIn('@lastEventForChildA', 'bob')
                ->assertSeeIn('@lastEventForChildB', 'bob')
                ->tap(function ($b) {
                    $this->assertEquals(['bob'], $b->script('return window.lastFooEventValue'));
                })

                /**
                 * receive event from action fired only to ancestors, and make sure global listener doesnt receive it
                 */
                ->waitForLivewire()->click('@emit.law')
                ->pause(350)
                ->assertSeeIn('@lastEventForParent', 'law')
                ->assertSeeIn('@lastEventForChildA', 'bob')
                ->assertSeeIn('@lastEventForChildB', 'bob')
                ->tap(function ($b) {
                    $this->assertEquals(['bob'], $b->script('return window.lastFooEventValue'));
                })

                /**
                 * receive event from action fired only to component name, and make sure global listener doesnt receive it
                 */
                ->waitForLivewire()->click('@emit.blog')
                ->pause(350)
                ->assertSeeIn('@lastEventForParent', 'law')
                ->assertSeeIn('@lastEventForChildA', 'bob')
                ->assertSeeIn('@lastEventForChildB', 'blog')
                ->tap(function ($b) {
                    $this->assertEquals(['bob'], $b->script('return window.lastFooEventValue'));
                })
            ;
        });
    }
}

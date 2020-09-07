<?php

namespace Tests\Browser\GlobalLivewire;

use Laravel\Dusk\Browser;
use Livewire\Livewire;
use Tests\Browser\TestCase;

class Test extends TestCase
{
    public function test()
    {
        $this->browse(function ($browser) {
            Livewire::visit($browser, Component::class)
                /**
                 * Event listeners are removed on teardown.
                 **/
                ->pause(250)
                ->tap(function ($b) { $b->script('window.livewire.stop()'); })
                ->click('@foo')
                ->pause(100)
                ->assertDontSeeIn('@output', 'foo')
                ->refresh()

                /**
                 * Rescanned components dont register twice.
                 **/
                ->tap(function ($b) { $b->script("livewire.rescan()"); })
                ->waitForLivewire()->click('@foo')
                ->assertSeeIn('@output', 'foo')
                ->refresh()

                /**
                 * window.livewire.onLoad callback is called when Livewire is initialized
                 */
                ->tap(function (Browser $browser) {
                    $this->assertTrue($browser->driver->executeScript('return window.isLoaded === true'), "livewire.onLoad wasn't called");
                })

                /**
                 * livewire:load DOM event is fired after start
                 */
                ->tap(function (Browser $browser) {
                    $this->assertTrue($browser->driver->executeScript('return window.loadEventWasFired === true'), "livewire:load wasn't triggered");
                })
            ;
        });
    }
}

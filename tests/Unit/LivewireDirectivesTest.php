<?php

namespace Tests\Unit;

use Livewire\Livewire;
use Livewire\Component;
use Illuminate\Testing\TestResponse;
use Illuminate\Support\Facades\Artisan;

class LivewireDirectivesTest extends TestCase
{
    /** @test */
    public function component_is_loaded_with_blade_directive()
    {
        Artisan::call('make:livewire', ['name' => 'foo']);

        $output = view('render-component', [
            'component' => 'foo',
        ])->render();

        $this->assertStringContainsString('div', $output);
    }

    /** @test */
    public function can_assert_see_livewire_on_standard_blade_view()
    {
        Artisan::call('make:livewire', ['name' => 'foo']);

        $fakeClass = new class {
            public function getContent()
            {
                return view('render-component', [
                    'component' => 'foo',
                ])->render();
            }
        };

        $testResponse = new TestResponse($fakeClass);

        $testResponse->assertSeeLivewire('foo');
    }

    /** @test */
    public function can_assert_dont_see_livewire_on_standard_blade_view()
    {
        $fakeClass = new class {
            public function getContent()
            {
                return view('null-view')->render();
            }
        };

        $testResponse = new TestResponse($fakeClass);

        $testResponse->assertDontSeeLivewire('foo');
    }

    /** @test */
    public function component_is_loaded_with_blade_directive_by_classname()
    {
        Artisan::call('make:livewire', ['name' => 'foo']);

        $output = view('render-component', [
            'component' => \App\Http\Livewire\Foo::class,
        ])->render();

        $this->assertStringContainsString('div', $output);
    }

    /** @test */
    public function this_directive_returns_javascript_component_object_string()
    {
        Livewire::test(ComponentForTestingDirectives::class)
            ->assertDontSee('@this')
            ->assertSee('window.livewire.find(');
    }

    /** @test */
    public function this_directive_can_be_used_in_nested_blade_component()
    {
        Livewire::test(ComponentForTestingNestedThisDirective::class)
            ->assertDontSee('@this')
            ->assertSee('window.livewire.find(');
    }
}

class ComponentForTestingDirectives extends Component
{
    public function render()
    {
        return view('this-directive');
    }
}

class ComponentForTestingNestedThisDirective extends Component
{
    public function render()
    {
        return view('nested-this-directive');
    }
}

<?php

namespace Tests\Feature;

use App\Livewire\UserManagement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create some roles
        Role::create(['name' => 'Admin', 'guard_name' => 'web']);
        Role::create(['name' => 'Student', 'guard_name' => 'web']);
        Role::create(['name' => 'Manager', 'guard_name' => 'web']);
    }

    /** @test */
    public function it_can_search_users_by_name()
    {
        // Arrange
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
        User::factory()->create(['name' => 'Bob Johnson', 'email' => 'bob@example.com']);

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->set('search', 'John')
            ->assertSee('John Doe')
            ->assertSee('Bob Johnson')
            ->assertDontSee('Jane Smith');
    }

    /** @test */
    public function it_can_search_users_by_email()
    {
        // Arrange
        User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->set('search', 'jane@example')
            ->assertSee('Jane Smith')
            ->assertDontSee('John Doe');
    }

    /** @test */
    public function it_can_filter_users_by_role()
    {
        // Arrange
        $admin = User::factory()->create(['name' => 'Admin User', 'role' => 'Admin']);
        $student = User::factory()->create(['name' => 'Student User', 'role' => 'Student']);

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->set('roleFilter', 'Admin')
            ->assertSee('Admin User')
            ->assertDontSee('Student User');
    }

    /** @test */
    public function it_can_filter_users_by_spatie_role()
    {
        // Arrange
        $admin = User::factory()->create(['name' => 'Admin User']);
        $admin->assignRole('Admin');
        
        $student = User::factory()->create(['name' => 'Student User']);
        $student->assignRole('Student');

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->set('roleFilter', 'Student')
            ->assertSee('Student User')
            ->assertDontSee('Admin User');
    }

    /** @test */
    public function it_can_combine_search_and_filter()
    {
        // Arrange
        $adminJohn = User::factory()->create(['name' => 'John Admin', 'role' => 'Admin']);
        $adminJane = User::factory()->create(['name' => 'Jane Admin', 'role' => 'Admin']);
        $studentJohn = User::factory()->create(['name' => 'John Student', 'role' => 'Student']);

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->set('search', 'John')
            ->set('roleFilter', 'Admin')
            ->assertSee('John Admin')
            ->assertDontSee('Jane Admin')
            ->assertDontSee('John Student');
    }

    /** @test */
    public function it_can_clear_filters()
    {
        // Arrange
        User::factory()->create(['name' => 'Admin User', 'role' => 'Admin']);
        User::factory()->create(['name' => 'Student User', 'role' => 'Student']);

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->set('search', 'Admin')
            ->set('roleFilter', 'Admin')
            ->call('clearFilters')
            ->assertSet('search', '')
            ->assertSet('roleFilter', '')
            ->assertSee('Admin User')
            ->assertSee('Student User');
    }

    /** @test */
    public function it_resets_pagination_when_searching()
    {
        // Arrange
        User::factory()->count(20)->create();

        // Act & Assert
        $component = Livewire::test(UserManagement::class);
        
        // Go to page 2
        $component->set('page', 2);
        
        // Search should reset to page 1
        $component->set('search', 'test');
        
        $component->assertSet('page', 1);
    }

    /** @test */
    public function it_displays_pagination_info()
    {
        // Arrange
        User::factory()->count(20)->create();

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->assertSee('Showing')
            ->assertSee('to')
            ->assertSee('of')
            ->assertSee('results');
    }

    /** @test */
    public function it_shows_no_results_message_when_search_has_no_matches()
    {
        // Arrange
        User::factory()->create(['name' => 'John Doe']);

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->set('search', 'NonexistentUser')
            ->assertSee('No users found matching your filters');
    }

    /** @test */
    public function search_is_case_insensitive()
    {
        // Arrange
        User::factory()->create(['name' => 'John Doe', 'email' => 'JOHN@EXAMPLE.COM']);

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->set('search', 'john')
            ->assertSee('John Doe');
            
        Livewire::test(UserManagement::class)
            ->set('search', 'JOHN@example.com')
            ->assertSee('John Doe');
    }

    /** @test */
    public function it_updates_user_count_based_on_filters()
    {
        // Arrange
        User::factory()->count(5)->create(['role' => 'Admin']);
        User::factory()->count(3)->create(['role' => 'Student']);

        // Act & Assert
        Livewire::test(UserManagement::class)
            ->assertSee('8 users')
            ->set('roleFilter', 'Admin')
            ->assertSee('5 users');
    }
}

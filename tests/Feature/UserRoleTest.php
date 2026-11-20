<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_is_cast_to_the_user_role_enum(): void
    {
        $user = User::factory()->customer()->create();

        $this->assertSame(UserRole::Customer, $user->fresh()->role);
        $this->assertSame('Customer', $user->fresh()->role->label());
    }

    public function test_factory_states_produce_the_expected_roles(): void
    {
        $this->assertTrue(User::factory()->create()->isAdmin());
        $this->assertSame(UserRole::Tenant, User::factory()->tenant()->create()->role);
        $this->assertSame(UserRole::Customer, User::factory()->customer()->create()->role);
    }

    public function test_role_helpers_distinguish_each_role(): void
    {
        $admin = User::factory()->create();
        $tenant = User::factory()->tenant()->create();
        $customer = User::factory()->customer()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($admin->isTenant());
        $this->assertFalse($admin->isCustomer());

        $this->assertTrue($tenant->isTenant());
        $this->assertFalse($tenant->isCustomer());

        $this->assertTrue($customer->isCustomer());
        $this->assertFalse($customer->isTenant());

        $this->assertFalse($admin->canAccessPortal());
        $this->assertTrue($tenant->canAccessPortal());
        $this->assertTrue($customer->canAccessPortal());
    }

    public function test_a_tenant_account_belongs_to_its_tenant_record(): void
    {
        $tenant = Tenant::factory()->create();

        $user = User::factory()->tenant()->create(['tenant_id' => $tenant->id]);

        $this->assertTrue($user->tenant->is($tenant));
        $this->assertNull(User::factory()->customer()->create()->tenant);
    }

    public function test_seeder_creates_the_three_demo_accounts(): void
    {
        $this->seed();

        $this->assertSame(UserRole::Admin, User::where('email', 'admin@example.com')->firstOrFail()->role);
        $this->assertSame(UserRole::Tenant, User::where('email', 'tenant@example.com')->firstOrFail()->role);
        $this->assertSame(UserRole::Customer, User::where('email', 'customer@example.com')->firstOrFail()->role);
    }
}

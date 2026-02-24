<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Tkp\Tkp;
use Tests\TestCase;

class TkpAuthorizationTest extends TestCase
{
    protected User $user1;
    protected User $user2;
    protected Tkp $tkp1;
    protected Tkp $tkp2;

    protected function setUp(): void
    {
        parent::setUp();

        // Создаём двух пользователей
        $this->user1 = User::factory()->create(['email' => 'user1@test.com']);
        $this->user2 = User::factory()->create(['email' => 'user2@test.com']);

        // Создаём ТКП для каждого пользователя
        $this->tkp1 = Tkp::create([
            'tkp_version' => 1,
            'user_id' => $this->user1->id,
            'project_name' => 'Project 1',
            'client_name' => 'Client 1',
        ]);

        $this->tkp2 = Tkp::create([
            'tkp_version' => 2,
            'user_id' => $this->user2->id,
            'project_name' => 'Project 2',
            'client_name' => 'Client 2',
        ]);
    }

    /**
     * Проверить что владелец может просмотреть свою ТКП
     */
    public function test_owner_can_view_own_tkp(): void
    {
        $this->actingAs($this->user1);
        $this->assertTrue($this->user1->can('view', $this->tkp1));
    }

    /**
     * Проверить что чужой пользователь не может просмотреть ТКП
     */
    public function test_user_cannot_view_other_tkp(): void
    {
        $this->actingAs($this->user2);
        $this->assertFalse($this->user2->can('view', $this->tkp1));
    }

    /**
     * Проверить что владелец может редактировать свою ТКП
     */
    public function test_owner_can_update_own_tkp(): void
    {
        $this->actingAs($this->user1);
        $this->assertTrue($this->user1->can('update', $this->tkp1));
    }

    /**
     * Проверить что чужой пользователь не может редактировать ТКП
     */
    public function test_user_cannot_update_other_tkp(): void
    {
        $this->actingAs($this->user2);
        $this->assertFalse($this->user2->can('update', $this->tkp1));
    }

    /**
     * Проверить что владелец может удалить свою ТКП
     */
    public function test_owner_can_delete_own_tkp(): void
    {
        $this->actingAs($this->user1);
        $this->assertTrue($this->user1->can('delete', $this->tkp1));
    }

    /**
     * Проверить что чужой пользователь не может удалить ТКП
     */
    public function test_user_cannot_delete_other_tkp(): void
    {
        $this->actingAs($this->user2);
        $this->assertFalse($this->user2->can('delete', $this->tkp1));
    }

    /**
     * Проверить что авторизованный пользователь может создать ТКП
     */
    public function test_user_can_create_tkp(): void
    {
        $this->actingAs($this->user1);
        $this->assertTrue($this->user1->can('create', Tkp::class));
    }
}

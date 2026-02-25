<?php

use App\Models\Configuration\Node;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // автоматическое создание стандартных групп для узлов в конфигураторе
        Node::create(['node_group_id' => 1, 'name' => 'ЧРП', 'type' => 'chrp', 'image' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NCA2NCI+PHBhdGggZD0iTTM2IDIgMTQgMzRoMTRsLTQgMjggMjYtMzZIMzZ6Ii8+PC9zdmc+', 'endpoints' => '[{"anchor":{"anchor_x":0.5,"anchor_y":-0.1,"anchor_dx":0,"anchor_dy":-1},"isSource":true,"isTarget":true},{"anchor":{"anchor_x":0.5,"anchor_y":1.1,"anchor_dx":0,"anchor_dy":1},"isSource":true,"isTarget":true}]']);
        Node::create(['node_group_id' => 2, 'name' => 'КСО', 'type' => 'kso', 'image' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NCA2NCI+PHBhdGggZD0iTTM2IDIgMTQgMzRoMTRsLTQgMjggMjYtMzZIMzZ6Ii8+PC9zdmc+', 'endpoints' => '[{"anchor":{"anchor_x":0.5,"anchor_y":-0.1,"anchor_dx":0,"anchor_dy":-1},"isSource":true,"isTarget":true},{"anchor":{"anchor_x":0.5,"anchor_y":1.1,"anchor_dx":0,"anchor_dy":1},"isSource":true,"isTarget":true}]']);
        Node::create(['node_group_id' => 4, 'name' => 'УПП', 'type' => 'upp', 'image' => 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCA2NCA2NCI+PHBhdGggZD0iTTM2IDIgMTQgMzRoMTRsLTQgMjggMjYtMzZIMzZ6Ii8+PC9zdmc+', 'endpoints' => '[{"anchor":{"anchor_x":0.5,"anchor_y":-0.1,"anchor_dx":0,"anchor_dy":-1},"isSource":true,"isTarget":true},{"anchor":{"anchor_x":0.5,"anchor_y":1.1,"anchor_dx":0,"anchor_dy":1},"isSource":true,"isTarget":true}]']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

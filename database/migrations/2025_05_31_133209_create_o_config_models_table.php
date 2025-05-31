<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Obelaw\Twist\Base\BaseMigration;

return new class extends BaseMigration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('o_configs', $this->prefix . 'o_configs');

        Schema::create($this->prefix . 'o_config_models', function (Blueprint $table) {
            $table->morphs('modelable');
            $table->string('path')->index();
            $table->json('value');
            $table->unique(['modelable_id', 'modelable_type', 'path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename($this->prefix . 'o_configs', 'o_configs');

        Schema::dropIfExists($this->prefix . 'o_config_models');
    }
};

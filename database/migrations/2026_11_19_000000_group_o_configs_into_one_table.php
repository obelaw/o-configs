<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Obelaw\Twist\Base\BaseMigration;

/**
 * v2: one o_configs table with a `group` column.
 *
 * Global values keep group '' and each model's values (o_config_models) move
 * to group "{modelable_type}:{modelable_id}", the key HasConfigs uses, then
 * o_config_models is dropped.
 */
return new class extends BaseMigration
{
    public function up(): void
    {
        $configs = $this->prefix . 'o_configs';
        $models = $this->prefix . 'o_config_models';
        $staging = $this->prefix . 'o_configs_v2';

        Schema::create($staging, function (Blueprint $table) use ($configs) {
            $table->id();
            $table->string('group', 191)->default('');
            $table->string('path');
            $table->json('value')->nullable();
            $table->unique(['group', 'path'], $configs . '_grp_path_unique');
            $table->index('path', $configs . '_path_idx');
        });

        $this->copy(DB::table($configs)->orderBy('path'), $staging, fn ($row) => '');

        if (Schema::hasTable($models)) {
            $this->copy(
                DB::table($models)->orderBy('modelable_type')->orderBy('modelable_id')->orderBy('path'),
                $staging,
                fn ($row) => $row->modelable_type . ':' . $row->modelable_id,
            );

            Schema::drop($models);
        }

        Schema::drop($configs);
        Schema::rename($staging, $configs);
    }

    public function down(): void
    {
        $configs = $this->prefix . 'o_configs';
        $models = $this->prefix . 'o_config_models';
        $staging = $this->prefix . 'o_configs_v1';

        Schema::create($staging, function (Blueprint $table) {
            $table->string('path')->index()->unique();
            $table->json('value');
        });

        Schema::create($models, function (Blueprint $table) {
            $table->morphs('modelable');
            $table->string('path')->index();
            $table->json('value');
            $table->unique(['modelable_id', 'modelable_type', 'path']);
        });

        DB::table($configs)->orderBy('id')->chunk(500, function ($rows) use ($staging, $models) {
            foreach ($rows as $row) {
                if ($row->group === '') {
                    DB::table($staging)->insert(['path' => $row->path, 'value' => $row->value]);

                    continue;
                }

                $split = strrpos($row->group, ':');
                if ($split === false) {
                    continue; // a group v1 has no place for
                }

                DB::table($models)->insert([
                    'modelable_type' => substr($row->group, 0, $split),
                    'modelable_id' => substr($row->group, $split + 1),
                    'path' => $row->path,
                    'value' => $row->value,
                ]);
            }
        });

        Schema::drop($configs);
        Schema::rename($staging, $configs);
    }

    /**
     * Copy rows into the staging table in batches, with their group.
     */
    private function copy($query, string $into, callable $group): void
    {
        $query->chunk(500, function ($rows) use ($into, $group) {
            $batch = [];

            foreach ($rows as $row) {
                $batch[] = ['group' => $group($row), 'path' => $row->path, 'value' => $row->value];
            }

            if ($batch) {
                DB::table($into)->insert($batch);
            }
        });
    }
};

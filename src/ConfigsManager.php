<?php

namespace Obelaw\Configs;

use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\Builder;
use InvalidArgumentException;
use Obelaw\Configs\Contracts\GroupResolver;
use Obelaw\Configs\Resolvers\GlobalGroupResolver;
use Obelaw\Configs\Services\ConfigurationService;

/**
 * Behind OConfig / oconfig(): one ConfigurationService per group.
 *
 * Calls without a group go to the group the configured resolver returns
 * (config `o-configs.group_resolver`), so packages calling oconfig() follow
 * whatever the app decided: per tenant, per store, or global.
 */
class ConfigsManager
{
    /** @var array<string, ConfigurationService> */
    private array $groups = [];

    public function __construct(private Container $app) {}

    /**
     * The values of a group (the global values when null).
     */
    public function group(?string $group): ConfigurationService
    {
        $group = ($group === null || $group === '') ? null : $group;

        return $this->groups[$group ?? ''] ??= new ConfigurationService(
            $group,
            $this->connection(),
            $group !== null && $this->app['config']->get('o-configs.fallback_to_global', true) ? $this->global() : null,
        );
    }

    /**
     * The global values.
     */
    public function global(): ConfigurationService
    {
        return $this->group(null);
    }

    /**
     * The values of the group the resolver returns now.
     */
    public function current(): ConfigurationService
    {
        return $this->group($this->currentGroup());
    }

    public function currentGroup(): ?string
    {
        return $this->resolver()->current();
    }

    /**
     * Rows of a group, for bulk work such as deleting a model's values.
     */
    public function query(?string $group): Builder
    {
        return $this->group($group)->query();
    }

    /**
     * Forget the loaded values (they are read again on the next call).
     */
    public function flush(): void
    {
        $this->groups = [];
    }

    public function all()
    {
        return $this->current()->all();
    }

    public function has(string $path)
    {
        return $this->current()->has($path);
    }

    public function get(string $path, $default = null)
    {
        return $this->current()->get($path, $default);
    }

    public function set(string $path, $value)
    {
        return $this->current()->set($path, $value);
    }

    public function forget(string $path)
    {
        return $this->current()->forget($path);
    }

    protected function connection(): ?string
    {
        $connection = $this->app['config']->get('o-configs.connection');

        return is_string($connection) && $connection !== '' ? $connection : null;
    }

    protected function resolver(): GroupResolver
    {
        $resolver = $this->app->make($this->app['config']->get('o-configs.group_resolver') ?: GlobalGroupResolver::class);

        if (! $resolver instanceof GroupResolver) {
            throw new InvalidArgumentException('o-configs.group_resolver must implement '.GroupResolver::class.'.');
        }

        return $resolver;
    }
}

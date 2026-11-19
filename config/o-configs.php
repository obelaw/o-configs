<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Group resolver
    |--------------------------------------------------------------------------
    |
    | Decides whose values OConfig / oconfig() use: a class implementing
    | Obelaw\Configs\Contracts\GroupResolver, built by the container. The
    | default keeps everything global. A multi-tenant app returns the current
    | tenant's group (e.g. $tenant->configGroup()) so every package's values
    | stay per tenant.
    |
    */

    'group_resolver' => Obelaw\Configs\Resolvers\GlobalGroupResolver::class,

    /*
    |--------------------------------------------------------------------------
    | Fall back to the global values
    |--------------------------------------------------------------------------
    |
    | When a group has no value, read the global one before the default.
    | Writes always go to the current group.
    |
    */

    'fallback_to_global' => true,

    /*
    |--------------------------------------------------------------------------
    | Database connection
    |--------------------------------------------------------------------------
    |
    | Connection of the o_configs table. Null uses the model's connection
    | (the Twist connection when one is set, otherwise the default). Set the
    | central connection to keep configuration out of tenant databases.
    |
    */

    'connection' => env('OCONFIGS_CONNECTION'),

];

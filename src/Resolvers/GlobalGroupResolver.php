<?php

namespace Obelaw\Configs\Resolvers;

use Obelaw\Configs\Contracts\GroupResolver;

/**
 * The default resolver: everything uses the global values.
 */
class GlobalGroupResolver implements GroupResolver
{
    public function current(): ?string
    {
        return null;
    }
}

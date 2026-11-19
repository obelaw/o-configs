<?php

namespace Obelaw\Configs\Contracts;

/**
 * Answers "whose configuration is this request / job working with?".
 *
 * OConfig / oconfig() read and write the group returned here, so an app can
 * keep values apart (per tenant, per store, …) while packages keep calling
 * oconfig() without knowing about it. Null means the global values.
 */
interface GroupResolver
{
    public function current(): ?string;
}

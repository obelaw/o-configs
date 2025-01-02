<?php

namespace Obelaw\Configs\Schema\Fields;

class Text
{
    public static function make(string $name, string $path, string $label, string $placeholder)
    {
        return [
            'name' => $name,
            'path' => $path,
            'label' => $label,
            'placeholder' => $placeholder,
        ];
    }
}

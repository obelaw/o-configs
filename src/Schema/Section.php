<?php

namespace Obelaw\Configs\Schema;

use Closure;

class Section
{
    public static $sections = [];

    public static function make(string $name, Closure $schema, string $description = null)
    {
        self::$sections[$name] = [
            'name' => $name,
            'schema' => $schema(),
            'description' => $description,
        ];
    }

    public static function getSections()
    {
        return self::$sections;
    }

    // Get kay value pair of the sections
    public static function getSectionsKeyValueConfigs()
    {
        $sections = self::getSections();

        $sectionsKeyValue = [];

        foreach ($sections as $section) {
            foreach ($section['schema'] as $_section) {
                $sectionsKeyValue[$_section['name']] = $_section['path'];
            }
        }

        return $sectionsKeyValue;
    }
}

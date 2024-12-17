<?php

namespace Grandeljay\WordpressIntegration;

class Taxonomy extends Entity
{
    public static function getDefaultFields(): array
    {
        $fields_defaults = \array_merge(
            parent::getDefaultFields(),
            [
                /** WordPress */
                'name',
            ]
        );

        return $fields_defaults;
    }

    public static function getDefaultOptions(): array
    {
        $options_default = \array_merge(
            parent::getDefaultOptions(),
            [
                /** WordPress */
                '_fields'    => self::getDefaultFields(),
                'hide_empty' => true,
            ]
        );

        return $options_default;
    }
}

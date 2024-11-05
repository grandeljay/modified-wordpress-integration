<?php

namespace Grandeljay\WordpressIntegration;

class Taxonomy extends Entity
{
    public static function getDefaultOptions(): array
    {
        $options_default = \array_merge(
            parent::getDefaultOptions(),
            [
                /** WordPress */
                'hide_empty' => true,
            ]
        );

        return $options_default;
    }
}

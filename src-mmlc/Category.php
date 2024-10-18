<?php

namespace Grandeljay\WordpressIntegration;

class Category
{
    private string $name;

    public function __construct(array $category)
    {
        $this->name = $category['name'];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
        ];
    }
}

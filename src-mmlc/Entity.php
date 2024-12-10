<?php

namespace Grandeljay\WordpressIntegration;

class Entity
{
    public static function getDefaultFields(): array
    {
        $fields_defaults = [
            /** WordPress */
            'id',

            /** Polylang */
            'lang',
            'translations',
        ];

        return $fields_defaults;
    }

    public static function getDefaultOptions(): array
    {
        $options_default = [
            /** WordPress */
            '_fields'  => \implode(',', self::getDefaultFields()),
            'per_page' => 100,

            /** Polylang */
            'lang'     => Blog::getLanguageCode(),
        ];

        return $options_default;
    }

    private int $id;

    protected array $translations;

    public function __construct(protected array $response_data)
    {
        $this->setId();
        $this->setTranslations();
    }

    private function setId(): void
    {
        $this->id = $this->response_data['id'];
    }

    private function setTranslations(): void
    {
        $this->translations = $this->response_data['translations'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->getId(),
            'translations' => $this->getTranslations(),
        ];
    }
}

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
    private array $translations;
    private string $language_code;

    public function __construct(protected array $response_data)
    {
        $this->setId();
        $this->setTranslations();
        $this->setLanguageCode();
    }

    private function setId(): void
    {
        $this->id = $this->response_data['id'];
    }

    private function setTranslations(): void
    {
        $this->translations = $this->response_data['translations'];
    }

    private function setLanguageCode(): void
    {
        $this->language_code = $this->response_data['lang'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function getLanguageCode(): string
    {
        return $this->language_code;
    }

    public function isInCurrentLanguage(): bool
    {
        $language_code_current = Blog::getLanguageCode();
        $language_code_entity  = $this->getLanguageCode();

        $entity_is_current_language = $language_code_current === $language_code_entity;

        return $entity_is_current_language;
    }

    public function existsInCurrentLanguage(): bool
    {
        $language_code_current = Blog::getLanguageCode();
        $translations          = $this->getTranslations();

        $exists_in_current_language = isset($translations[$language_code_current]);

        return $exists_in_current_language;
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->getId(),
            'translations' => $this->getTranslations(),
        ];
    }
}

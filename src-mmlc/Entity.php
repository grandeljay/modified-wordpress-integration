<?php

namespace Grandeljay\WordpressIntegration;

class Entity
{
    public static function getDefaultOptions(): array
    {
        $language_code = $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE;

        $options_default = [
            /** Polylang */
            'lang'     => $language_code,

            /** WordPress */
            'per_page' => 100,
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

    public function getIdForLanguage(string $language_code = null): int
    {
        if (null === $language_code) {
            $language_code = $_SESSION['language_code'] ?? \DEFAULT_LANGUAGE;
        }

        $id_for_language = $this->translations[$language_code] ?? $this->id;

        return $id_for_language;
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

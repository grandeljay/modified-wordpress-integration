<?php

namespace Grandeljay\WordpressIntegration;

class Entity
{
    public static function getDefaultOptions(): array
    {
        $options_default = [
            /** Polylang */
            'lang'     => Blog::getLanguageCode(),

            /** WordPress */
            'per_page' => 100,
        ];

        return $options_default;
    }

    public static function getTranslation(array $entities, int $entity_id): self
    {
        foreach ($entities as $category) {
            $translations_ids = $category->getTranslations();

            foreach ($translations_ids as $language_code => $id) {
                if ($id === $entity_id) {
                    return $category;
                }
            }
        }

        throw new \Exception(
            \sprintf('%s not found with the ID %d.', self::class, $entity_id)
        );
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

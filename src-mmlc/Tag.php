<?php

namespace Grandeljay\WordpressIntegration;

class Tag
{
    private int $id;
    private string $name;
    private string $link;
    private array $translations;

    public function __construct(private array $response_data)
    {
        $this->setId();
        $this->setName();
        $this->setLink();
        $this->setTranslations();
    }

    private function setId(): void
    {
        $this->id = $this->response_data['id'];
    }

    private function setName(): void
    {
        $this->name = $this->response_data['name'];
    }

    private function setLink(): void
    {
        $link = new Url(Constants::BLOG_URL_POSTS);
        $link->addDefaultParameters();
        $link->addParameters(['tag_id' => $this->id]);

        $this->link = $link->toString();
    }

    private function setTranslations(): void
    {
        $this->translations = $this->response_data['translations'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function toArray(): array
    {
        return [
            'id'           => $this->getId(),
            'name'         => $this->getName(),
            'link'         => $this->getLink(),
            'translations' => $this->getTranslations(),
        ];
    }
}

<?php

namespace Grandeljay\WordpressIntegration;

class Category
{
    private int $id;
    private string $name;
    private string $link;
    private array $translations;
    private Media $featured_image;

    public function __construct(private array $response_data)
    {
        $this->setId();
        $this->setName();
        $this->setLink();
        $this->setTranslations();
        $this->setFeaturedImage();
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
        $link->addParameters(['category_id' => $this->id]);

        $this->link = $link->toString();
    }

    private function setTranslations(): void
    {
        $this->translations = $this->response_data['translations'];
    }

    private function setFeaturedImage(): void
    {
        if (!isset($this->response_data['featured_image']['id']) || 0 === $this->response_data['featured_image']['id']) {
            return;
        }

        $endpoint = Constants::BLOG_URL_API_MEDIA . $this->response_data['featured_image']['id'];

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return;
        }

        $media_wp = $url->getRequestBody();
        $media    = new Media($media_wp);

        $this->featured_image = $media;
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

    public function getFeaturedImage(): Media|null
    {
        if (!isset($this->featured_image)) {
            return null;
        }

        return $this->featured_image;
    }

    public function toArray(): array
    {
        $featured_image = $this->getFeaturedImage();

        if ($featured_image instanceof Media) {
            $featured_image = $featured_image->toArray();
        }

        return [
            'id'             => $this->getId(),
            'name'           => $this->getName(),
            'link'           => $this->getLink(),
            'translations'   => $this->getTranslations(),
            'featured_image' => $featured_image,
        ];
    }
}

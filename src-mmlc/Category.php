<?php

namespace Grandeljay\WordpressIntegration;

class Category extends Entity
{
    private string $name;
    private string $link;
    private Media $featured_image;

    public function __construct(array $response_data)
    {
        parent::__construct($response_data);

        $this->setName();
        $this->setLink();
        $this->setFeaturedImage();
    }

    private function setName(): void
    {
        $this->name = $this->response_data['name'];
    }

    private function setLink(): void
    {
        $link = new Url(Constants::BLOG_URL_POSTS);
        $link->addDefaultParameters();
        $link->addParameters(['category_id' => $this->getId()]);

        $this->link = $link->toString();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getLink(): string
    {
        return $this->link;
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
        $array = parent::toArray();

        $featured_image = $this->getFeaturedImage();

        if ($featured_image instanceof Media) {
            $featured_image = $featured_image->toArray();
        }

        $array = \array_merge(
            $array,
            [
                'name'           => $this->getName(),
                'link'           => $this->getLink(),
                'featured_image' => $featured_image,
            ]
        );

        return $array;
    }
}

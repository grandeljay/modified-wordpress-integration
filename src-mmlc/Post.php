<?php

namespace Grandeljay\WordpressIntegration;

class Post
{
    private int $id;
    private string $title;
    private string $excerpt;
    private string $link;
    private string $content;
    private Media $featured_image;

    private string $language;
    private array $translations;

    private int $date_published;
    private int $date_modified;

    private array $categories = [];
    private array $tags       = [];

    public function __construct(private array $response_data)
    {
        $this->setId();
        $this->setTitle();
        $this->setExcerpt();
        $this->setLink();
        $this->setContent();
        $this->setLanguage();
        $this->setTranslations();
        $this->setFeaturedImage();
        $this->setDatePublished();
        $this->setDateModified();
    }

    private function setId(): void
    {
        $this->id = $this->response_data['id'];
    }

    private function setTitle(): void
    {
        $this->title = $this->response_data['title']['rendered'];
    }

    private function setExcerpt(): void
    {
        $this->excerpt = $this->response_data['excerpt']['rendered'];
    }

    private function setLink(): void
    {
        $link_server = \ENABLE_SSL ? \HTTPS_SERVER : \HTTP_SERVER;

        $link = new Url($link_server . Constants::BLOG_URL_POSTS);
        $link->addParameters(
            [
                'language' => $this->response_data['lang'],
                'post'     => $this->response_data['id'],
            ]
        );

        $this->link = $link->toString();
    }

    private function setContent(): void
    {
        $this->content = $this->response_data['content']['rendered'];
    }

    private function setLanguage(): void
    {
        $this->language = $this->response_data['lang'];
    }

    private function setTranslations(): void
    {
        $this->translations = $this->response_data['translations'];
    }

    private function setFeaturedImage(): void
    {
        $endpoint = $this->response_data['_links']['wp:featuredmedia'][0]['href'] ?? '';

        $url = new Url($endpoint);
        $url->makeRequest();

        if (!$url->isRequestSuccessful()) {
            return;
        }

        $media_wp = $url->getRequestBody();
        $media    = new Media($media_wp);

        $this->featured_image = $media;
    }

    private function setDatePublished(): void
    {
        $this->date_published = \strtotime($this->response_data['date']);
    }

    private function setDateModified(): void
    {
        $this->date_modified = \strtotime($this->response_data['modified']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function getFeaturedImage(): Media
    {
        return $this->featured_image;
    }

    public function getDatePublished(): int
    {
        return $this->date_published;
    }

    public function getDateModified(): int
    {
        return $this->date_modified;
    }

    public function getDateFormatted(int $timestamp, string $language_code = null): string
    {
        if (null === $language_code) {
            $language_code = $this->getLanguage();
        }

        $pattern = match ($language_code) {
            'de'    => 'd.m.o',
            'en'    => 'd/m/o',
            'es'    => 'd/m/o',
            'fr'    => 'd/m/o',
            'it'    => 'd/m/o',
            default => 'o-m-d',
        };

        $date_formatted = \date($pattern, $timestamp);

        return $date_formatted;
    }

    public function getCategories(): array
    {
        return $this->categories;
    }

    public function getTags(): array
    {
        return $this->tags;
    }

    public function setCategories(array $categories): void
    {
        foreach ($this->response_data['categories'] as $category_id) {
            if (isset($categories[$category_id])) {
                $this->categories[] = $categories[$category_id];
            }
        }
    }

    public function setTags(array $tags): void
    {
        foreach ($this->response_data['tags'] as $tag_id) {
            if (isset($tags[$tag_id])) {
                $this->tags[] = $tags[$tag_id];
            }
        }
    }

    public function toArray(): array
    {
        $featured_image = $this->getFeaturedImage()
                               ->toArray();

        $date_published = $this->getDateFormatted(
            $this->getDatePublished(),
            $this->getLanguage()
        );

        $categories = \array_map(
            function (Category $category) {
                return $category->toArray();
            },
            $this->getCategories()
        );

        $tags = \array_map(
            function (Tag $tag) {
                return $tag->toArray();
            },
            $this->getTags()
        );

        return [
            'title'          => $this->getTitle(),
            'excerpt'        => $this->getExcerpt(),
            'link'           => $this->getLink(),
            'content'        => $this->getContent(),
            'language'       => $this->getLanguage(),
            'featured_image' => $featured_image,
            'date_published' => $date_published,
            'categories'     => $categories,
            'tags'           => $tags,
        ];
    }
}

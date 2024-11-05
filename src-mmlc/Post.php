<?php

namespace Grandeljay\WordpressIntegration;

class Post extends Entity
{
    private string $title;
    private string $excerpt;
    private string $link;
    private string $content;
    private Media $featured_image;

    private string $language;

    private int $date_published;
    private int $date_modified;

    private array $categories = [];
    private array $tags       = [];

    public function __construct(array $response_data)
    {
        parent::__construct($response_data);

        $this->setTitle();
        $this->setExcerpt();
        $this->setLink();
        $this->setContent();
        $this->setLanguage();
        $this->setDatePublished();
        $this->setDateModified();

        $this->setFeaturedImage();
        $this->setTerms();
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

    private function setDatePublished(): void
    {
        $this->date_published = \strtotime($this->response_data['date']);
    }

    private function setDateModified(): void
    {
        $this->date_modified = \strtotime($this->response_data['modified']);
    }

    private function setFeaturedImage(): void
    {
        if (empty($this->response_data['_embedded']['wp:featuredmedia'])) {
            return;
        }

        $media_wp = $this->response_data['_embedded']['wp:featuredmedia'][0];
        $media    = new Media($media_wp);

        $this->featured_image = $media;
    }

    private function setTerms(): void
    {
        if (empty($this->response_data['_embedded']['wp:term'])) {
            return;
        }

        $this->setTermCategories();
        $this->setTermTags();
    }

    private function setTermCategories(): void
    {
        if (empty($this->response_data['_embedded']['wp:term'][0])) {
            return;
        }

        $this->categories = [];

        $categories_wp = $this->response_data['_embedded']['wp:term'][0];

        foreach ($categories_wp as $category_wp) {
            $category = new Category($category_wp);

            $this->categories[] = $category;
        }
    }

    private function setTermTags(): void
    {
        if (empty($this->response_data['_embedded']['wp:term'][1])) {
            return;
        }

        $this->tags = [];

        $tags_wp = $this->response_data['_embedded']['wp:term'][1];

        foreach ($tags_wp as $tag_wp) {
            $tag = new Tag($tag_wp);

            $this->tags[] = $tag;
        }
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

    public function getFeaturedImage(): Media|null
    {
        if (!isset($this->featured_image)) {
            return null;
        }

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

    public function toArray(): array
    {
        $array = parent::toArray();

        $featured_image = $this->getFeaturedImage();

        if ($featured_image instanceof Media) {
            $featured_image = $featured_image->toArray();
        }

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

        $array = \array_merge(
            $array,
            [
                'title'          => $this->getTitle(),
                'excerpt'        => $this->getExcerpt(),
                'link'           => $this->getLink(),
                'content'        => $this->getContent(),
                'language'       => $this->getLanguage(),
                'featured_image' => $featured_image,
                'date_published' => $date_published,
                'categories'     => $categories,
                'tags'           => $tags,
            ]
        );

        return $array;
    }
}

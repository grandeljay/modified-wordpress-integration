<?php

namespace Grandeljay\WordpressIntegration;

class Post
{
    private string $title;
    private string $excerpt;
    private string $link;
    private string $content;
    private string $language;
    private string $featured_image;
    private string $date_published;

    private array $categories = [];
    private array $tags       = [];

    public function __construct(private array $response_data)
    {
        $this->title    = $response_data['title']['rendered'];
        $this->excerpt  = $response_data['excerpt']['rendered'];
        $this->link     = Constants::BLOG_URL_POSTS . '?language=' . $_SESSION['language_code'] . '&post=' . $response_data['id'];
        $this->content  = $response_data['content']['rendered'];
        $this->language = $this->getLanguageWp($response_data['link']);

        if ($response_data['featured_media']) {
            $this->featured_image = $this->getFeaturedImageWp($response_data['featured_media']);
        } else {
            $this->featured_image = '';
        }

        $this->date_published = $this->getDatePublishedWp($response_data['date']);
    }

    private function getLanguageWp(string $link): string
    {
        $query = \parse_url($link, \PHP_URL_QUERY);
        \parse_str($query, $parameters);

        return $parameters['lang'];
    }

    private function getFeaturedImageWp(int $id): string
    {
        $media = Blog::getFeaturedImage($id);

        return $media['source_url'];
    }

    private function getDatePublishedWp(string $date): string
    {
        $pattern = match ($_SESSION['language_code']) {
            'de'    => 'd.m.y',
            'en'    => 'd/m/y',
            'es'    => 'd/m/y',
            'fr'    => 'd/m/y',
            'it'    => 'd/m/y',
            default => 'o-m-d',
        };

        return \date($pattern, \strtotime($date));
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

    public function getFeaturedImage(): string
    {
        return $this->featured_image;
    }

    public function getDatePublished(): string
    {
        return $this->date_published;
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
            'featured_image' => $this->getFeaturedImage(),
            'date_published' => $this->getDatePublished(),
            'categories'     => $categories,
            'tags'           => $tags,
        ];
    }
}

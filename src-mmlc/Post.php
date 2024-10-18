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
    private array $categories;

    public function __construct(array $post)
    {
        $this->title    = $post['title']['rendered'];
        $this->excerpt  = $post['excerpt']['rendered'];
        $this->link     = Constants::BLOG_URL_API_POSTS . '?language=' . $_SESSION['language_code'] . '&post=' . $post['id'];
        $this->content  = $post['content']['rendered'];
        $this->language = $this->getLanguageWp($post['link']);

        if ($post['featured_media']) {
            $this->featured_image = $this->getFeaturedImageWp($post['featured_media']);
        } else {
            $this->featured_image = '';
        }

        $this->date_published = $this->getDatePublishedWp($post['date']);
        $this->categories     = $this->getCategoriesWp($post['categories']);
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

    private function getCategoriesWp(array $category_ids): array
    {
        $categories = [];

        foreach ($category_ids as $category_id) {
            $categories[] = Blog::getCategory($category_id);
        }

        return $categories;
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

    public function toArray(): array
    {
        return [
            'title'          => $this->getTitle(),
            'excerpt'        => $this->getExcerpt(),
            'link'           => $this->getLink(),
            'content'        => $this->getContent(),
            'language'       => $this->getLanguage(),
            'featured_image' => $this->getFeaturedImage(),
            'date_published' => $this->getDatePublished(),
            'categories'     => $this->getCategories(),
        ];
    }
}

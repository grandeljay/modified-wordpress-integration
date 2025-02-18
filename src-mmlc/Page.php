<?php

namespace Grandeljay\WordpressIntegration;

class Page extends Entity
{
    public static function getDefaultFields(): array
    {
        $fields_defaults = \array_merge(
            parent::getDefaultFields(),
            [
                /** WordPress */
                'title',

                /** Polylang */
                'lang',
            ]
        );

        return $fields_defaults;
    }

    public static function getDefaultOptions(): array
    {
        $options_default = \array_merge(
            parent::getDefaultOptions(),
            [
                'per_page' => 8,
                'page'     => $_GET['page'] ?? 1,
                '_embed'   => true,
            ]
        );

        return $options_default;
    }

    private string $title;
    private string $excerpt;
    private string $content;

    public function __construct(array $response_data)
    {
        parent::__construct($response_data);

        $this->setTitle();
        $this->setExcerpt();
        $this->setContent();
    }

    private function setTitle(): void
    {
        $this->title = $this->response_data['title']['rendered'];
    }

    private function setExcerpt(): void
    {
        $this->excerpt = $this->response_data['excerpt']['rendered'];
    }

    private function setContent(): void
    {
        $this->content = $this->response_data['content']['rendered'];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array = \array_merge(
            $array,
            [
                'title'    => $this->getTitle(),
                'excerpt'  => $this->getExcerpt(),
                'content'  => $this->getContent(),
                'language' => $this->getLanguageCode(),
            ]
        );

        return $array;
    }
}

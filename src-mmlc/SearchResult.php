<?php

namespace Grandeljay\WordpressIntegration;

/**
 * A Search Result.
 *
 * @link https://developer.wordpress.org/rest-api/reference/search-results/
 */
class SearchResult
{
    private int $id;
    private string $title;
    private string $link;
    private string $type;
    private string $subtype;

    public function __construct(private array $response_data)
    {
        $this->setId();
        $this->setTitle();
        $this->setLink();
        $this->setType();
        $this->setSubtype();
    }

    private function setId(): void
    {
        $this->id = $this->response_data['id'];
    }

    private function setTitle(): void
    {
        $this->title = $this->response_data['title'];
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

    private function setType(): void
    {
        $this->type = $this->response_data['type'];
    }

    private function setSubtype(): void
    {
        $this->subtype = $this->response_data['subtype'];
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSubtype(): string
    {
        return $this->subtype;
    }

    public function toArray(): array
    {
        return [
            'id'      => $this->getId(),
            'title'   => $this->getTitle(),
            'link'    => $this->getLink(),
            'type'    => $this->getType(),
            'subtype' => $this->getSubtype(),
        ];
    }
}

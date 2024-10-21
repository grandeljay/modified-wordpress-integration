<?php

namespace Grandeljay\WordpressIntegration;

class Tag
{
    private int $id;
    private string $name;
    private string $link;

    public function __construct(private array $response_data)
    {
        $this->setId();
        $this->setName();
        $this->setLink();
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
        $link->addParameters(['tags' => $this->id]);

        $this->link = $link->toString();
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

    public function toArray(): array
    {
        return [
            'id'   => $this->getId(),
            'name' => $this->getName(),
            'link' => $this->getLink(),
        ];
    }
}

<?php

namespace Grandeljay\WordpressIntegration;

class Tag extends Taxonomy
{
    public static function getDefaultOptions(): array
    {
        $options_default = \array_merge(
            parent::getDefaultOptions(),
            []
        );

        return $options_default;
    }

    private string $name;
    private string $link;

    public function __construct(array $response_data)
    {
        parent::__construct($response_data);

        $this->setName();
        $this->setLink();
    }

    private function setName(): void
    {
        $this->name = $this->response_data['name'];
    }

    private function setLink(): void
    {
        $link = new Url(Constants::BLOG_URL_POSTS);
        $link->addDefaultParameters();
        $link->addParameters(['tag_id' => $this->getId()]);

        $this->link = $link->toString();
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
        $array = parent::toArray();
        $array = \array_merge(
            $array,
            [
                'name' => $this->getName(),
                'link' => $this->getLink(),
            ]
        );

        return $array;
    }
}

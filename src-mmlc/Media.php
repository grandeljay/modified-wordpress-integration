<?php

namespace Grandeljay\WordpressIntegration;

class Media
{
    private string $title;
    private string $alt_text;
    private string $source_url;

    public function __construct(array $media)
    {
        $this->title      = $media['title']['rendered'];
        $this->alt_text   = $media['alt_text'];
        $this->source_url = $media['source_url'];
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAltText(): string
    {
        return $this->alt_text;
    }

    public function getSourceUrl(): string
    {
        return $this->source_url;
    }

    public function toArray(): array
    {
        return [
            'title'      => $this->getTitle(),
            'alt_text'   => $this->getAltText(),
            'source_url' => $this->getSourceUrl(),
        ];
    }
}

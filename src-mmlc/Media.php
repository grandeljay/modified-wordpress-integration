<?php

namespace Grandeljay\WordpressIntegration;

class Media
{
    private string $title;
    private string $alt_text;
    private string $source_url;
    private array $media_details;

    public function __construct(array $media)
    {
        $this->title         = $media['title']['rendered'];
        $this->alt_text      = $media['alt_text'];
        $this->source_url    = $media['source_url'];
        $this->media_details = $media['media_details'] ?? [];
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

    private function getSizeFallback(): array
    {
        $fallback = [
            'file'       => $this->media_details['file'],
            'width'      => $this->media_details['width'],
            'height'     => $this->media_details['height'],
            'filesize'   => $this->media_details['filesize'],
            'mime_type'  => $this->media_details['mime_type'],
            'source_url' => $this->media_details['source_url'],
        ];

        return $fallback;
    }

    public function getSizeThumbnail(): array
    {
        $thumbnail =  $this->media_details['sizes']['thumbnail']
                   ?? $this->getSizeFallback();

        return $thumbnail;
    }

    public function getSizeMedium(): array
    {
        $medium =  $this->media_details['sizes']['medium']
                ?? $this->getSizeFallback();

        return $medium;
    }

    public function getSizeLarge(): array
    {
        $medium =  $this->media_details['sizes']['large']
                ?? $this->getSizeFallback();

        return $medium;
    }

    public function getSizeFull(): array
    {
        $medium =  $this->media_details['sizes']['full']
                ?? $this->getSizeFallback();

        return $medium;
    }

    public function toArray(): array
    {
        return [
            'title'      => $this->getTitle(),
            'alt_text'   => $this->getAltText(),
            'source_url' => $this->getSourceUrl(),
            'thumbnail'  => $this->getSizeThumbnail(),
            'medium'     => $this->getSizeMedium(),
            'large'      => $this->getSizeLarge(),
            'full'       => $this->getSizeFull(),
        ];
    }
}

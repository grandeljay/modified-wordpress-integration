<?php

namespace Grandeljay\WordpressIntegration;

class Category extends Taxonomy
{
    public static function getDefaultFields(): array
    {
        $fields_defaults = \array_merge(
            parent::getDefaultFields(),
            [
                /** WordPress */
                '_links',
                '_embedded',
            ]
        );

        return $fields_defaults;
    }

    public static function getDefaultOptions(): array
    {
        $options_default = \array_merge(
            parent::getDefaultOptions(),
            [
                /** WordPress */
                '_fields' => self::getDefaultFields(),
                '_embed'  => true,
                'page'    => 1,
            ]
        );

        /**
         * Categories must not use `_fields` until the custom featured
         * image is compatible with it.
         */
        // unset($options_default['_fields']);

        return $options_default;
    }

    private string $name;
    private string $link;
    private Media $featured_image;

    public function __construct(array $response_data)
    {
        parent::__construct($response_data);

        $this->setName();
        $this->setLink();

        $this->setFeaturedImage();
    }

    private function setName(): void
    {
        $this->name = $this->response_data['name'];
    }

    private function setLink(): void
    {
        $link = new Url(Constants::BLOG_URL_POSTS);
        $link->addDefaultParameters();
        $link->addParameters(['category_id' => $this->getId()]);

        $this->link = $link->toString();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function getFeaturedImage(): Media|null
    {
        if (!isset($this->featured_image)) {
            return null;
        }

        return $this->featured_image;
    }

    public function isUncategorised(): bool
    {
        $translations     = $this->getTranslations();
        $contains_id_1    = \in_array(1, $translations);
        $is_uncategorised = $contains_id_1;

        return $is_uncategorised;
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        $featured_image = $this->getFeaturedImage();

        if ($featured_image instanceof Media) {
            $featured_image = $featured_image->toArray();
        }

        $array = \array_merge(
            $array,
            [
                'name'           => $this->getName(),
                'link'           => $this->getLink(),
                'featured_image' => $featured_image,
            ]
        );

        return $array;
    }
}

<?php

namespace Grandeljay\WordpressIntegration;

class Tag extends Taxonomy
{
    public static function getDefaultFields(): array
    {
        $fields_defaults = \array_merge(
            parent::getDefaultFields(),
            []
        );

        return $fields_defaults;
    }

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
        if (isset($this->response_data['name'])) {
            $this->name = $this->response_data['name'];
        }
    }

    private function setLink(): void
    {
        $link = new Url(Constants::BLOG_URL_POSTS);
        $link->addDefaultParameters();
        $link->addParameters(['tag_id' => [$this->getId()]]);

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

    /**
     * Returns whether the current tag was queried via the url.
     *
     * @return bool
     */
    public function isActive(): bool
    {
        if (empty($_GET['tag_id'])) {
            return false;
        }

        $translations   = $this->getTranslations();
        $passed_tag_ids = $_GET['tag_id'];

        foreach ($passed_tag_ids as $passed_tag_id) {
            if (\in_array($passed_tag_id, $translations)) {
                return true;
            }
        }

        return false;
    }

    public function toArray(): array
    {
        $array = parent::toArray();
        $array = \array_merge(
            $array,
            [
                'name'      => $this->getName(),
                'link'      => $this->getLink(),
                'is_active' => $this->isActive(),
            ]
        );

        return $array;
    }
}

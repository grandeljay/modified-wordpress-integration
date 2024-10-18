<?php

/**
 * WordPress Integration
 *
 * @author  Jay Trees
 * @link    https://github.com/grandeljay/modified-wordpress-integration
 * @package GrandeljayWordpressIntegration
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */

use Grandeljay\WordpressIntegration\Constants;
use RobinTheHood\ModifiedStdModule\Classes\StdModule;

class grandeljay_wordpress_integration extends StdModule
{
    public const VERSION = '0.1.0';

    public function __construct()
    {
        parent::__construct(Constants::MODULE_NAME);

        $this->checkForUpdate(true);
    }

    public function display(): array
    {
        return $this->displaySaveButton();
    }

    public function install(): void
    {
        parent::install();
    }

    protected function updateSteps(): int
    {
        if (version_compare($this->getVersion(), self::VERSION, '<')) {
            $this->setVersion(self::VERSION);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }

    public function remove(): void
    {
        parent::remove();
    }
}

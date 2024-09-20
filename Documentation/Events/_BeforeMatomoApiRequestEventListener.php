<?php

declare(strict_types=1);

namespace YourVendor\YourExtension\EventListener;

use Brotkrueml\MatomoWidgets\Event\BeforeMatomoApiRequestEvent;
use Psr\Http\Message\ServerRequestInterface;
use YourVendor\YourExtension\Mapping\MatomoSiteMapper;

final class BeforeMatomoApiRequestEventListener
{
    public function __construct(
        private readonly MatomoSiteMapper $matomoSiteMapper,
    ) {}

    public function __invoke(BeforeMatomoApiRequestEvent $event): void
    {
        $hostName = $this->getRequest()->getServerParams()['REMOTE_HOST'];
        if ($idSiteFromHostName = $this->matomoSiteMapper->getIdSiteFromHostName($hostName)) {
            $event->setIdSite($idSiteFromHostName);
        }
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}

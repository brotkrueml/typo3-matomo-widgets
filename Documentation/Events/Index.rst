.. include:: /Includes.rst.txt

.. index:: Events

.. _events:

=============
PSR-14 events
=============

Target group: **Developers**

Have a look into the :ref:`event dispatcher documentation <t3coreapi:EventDispatcher>` if
you are not familiar with PSR-14 events.

BeforeMatomoApiRequestEvent
===========================

.. versionadded:: 1.7.0/2.1.0

This event provides a possibility to adjust the site ID and the authentication
token just before making the request to the Matomo API. This may be helpful
in a big multi-site installation where you added a :ref:`configuration
independent from a site <configuration-independent-from-site>`.

The event :php:`Brotkrueml\MatomoWidgets\Event\BeforeMatomoApiRequestEvent`
provides the following methods:

:php:`->getIdSite()`
   Returns the site ID.

:php:`->setIdSite()`
   Sets the site ID.

:php:`->getTokenAuth()`
   Returns the authentication token.

:php:`->setTokenAuth()`
   Sets the authentication token.

Example
-------

Dependent on the host name the current backend user is using we change the
site ID:

.. code-block:: php
   :caption: EXT:your_extension/Classes/EventListener/BeforeMatomoApiRequestEventListener.php

   namespace YourVendor\YourExtension\EventListener;

   use Psr\Http\Message\ServerRequestInterface;
   use YourVendor\YourExtension\Mapping\MatomoSiteMapper;

   final class BeforeMatomoApiRequestEventListener
   {
      private MatomoSiteMapper $matomoSiteMapper;

      public function __construct(MatomoSiteMapper $matomoSiteMapper)
      {
         $this->matomoSiteMapper = $matomoSiteMapper;
      }

      public function __invoke(BeforeMatomoApiRequestEvent $event): void
      {
         $hostName = $this->request->getServerParams()['REMOTE_HOST'];
         if ($idSiteFromHostName = $this->matomoSiteMapper->getIdSiteFromHostName($hostName)) {
            $event->setIdSite($idSiteFromHostName);
         }
      }

      private function getRequest(): ServerRequestInterface
      {
         return $GLOBALS['TYPO3_REQUEST'];
      }
   }

Registration of the event listener:

.. code-block:: yaml
   :caption: EXT:your_extension/Configuration/Services.yaml

   services:
      YourVendor\YourExtension\EventListener\BeforeMatomoApiRequestEventListener:
         tags:
            - name: event.listener
              identifier: 'myMatomoApiRequestListener'

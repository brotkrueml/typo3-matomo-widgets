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

This event provides a possibility to adjust the site ID and the authentication
token just before making the request to the Matomo API. This may be helpful
in a big multi-site installation where you added a :ref:`configuration
independent from a site <configuration-independent-from-site>`.

The event :php:`\Brotkrueml\MatomoWidgets\Event\BeforeMatomoApiRequestEvent`
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

.. literalinclude:: _BeforeMatomoApiRequestEventListener.php
   :caption: EXT:your_extension/Classes/EventListener/BeforeMatomoApiRequestEventListener.php

Registration of the event listener:

.. literalinclude:: _Services.yaml
   :caption: EXT:your_extension/Configuration/Services.yaml

Read :ref:`how to configure dependency injection in extensions <t3coreapi:dependency-injection-in-extensions>`.

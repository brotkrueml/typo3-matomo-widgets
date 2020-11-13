.. include:: /Includes.rst.txt

.. _migration:

=========
Migration
=========

From version 0.2 to 0.3
=======================

To allow the configuration of more than one Matomo instance the configuration
moved from the extension configuration to the :ref:`site management
<site-configuration>`.

.. attention::
   As with all upgrades: Please backup your data before executing the upgrade
   wizard!

The migration can be started in the TYPO3 backend via :guilabel:`Admin Tools` >
:guilabel:`Upgrade` > :guilabel:`Upgrade Wizard`.

.. figure:: /Images/SiteConfigurationMigrationBackend.png
   :alt: Upgrade wizard in backend
   :class: with-border

   Upgrade wizard in backend

Alternatively, you can also execute the migration wizard on a TYPO3 console:

.. figure:: /Images/SiteConfigurationMigrationConsole.png
   :alt: Upgrade wizard on console
   :class: with-border

   Upgrade wizard on console

.. note::
   If only one site is available the migration of the configuration can be done
   with a upgrade wizard. If there is more than one site configured you have to
   migrate the configuration by yourself. For this purpose the extension
   configuration is still available but has no effect at all.

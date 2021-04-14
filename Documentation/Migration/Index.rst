.. include:: /Includes.rst.txt

.. _migration:

=========
Migration
=========

.. important::
   Before updating from a version before 0.3 to 1.0 or up you should update
   to version 0.3.2 first and execute the upgrade wizards. Then update to the
   newest version and run the next upgrade wizards.

.. contents::
   :depth: 2
   :local:

From version 0.3 to 1.0
=======================

In version 1.0 the format changed how the active widgets for a site is stored
in the site configuration. For the migration of this configuration an upgrade
wizard is available.

.. attention::
   As with all upgrades: Please backup your data before executing the upgrade
   wizard!

The migration can be started in the TYPO3 backend via :guilabel:`Admin Tools` >
:guilabel:`Upgrade` > :guilabel:`Upgrade Wizard`.

.. figure:: /Images/EnableWidgetsMigrationBackend.png
   :alt: Migrate configuration in backend
   :class: with-border

   Migrate configuration in backend

Alternatively, you can also execute the migration wizard on a TYPO3 console:

.. figure:: /Images/EnableWidgetsMigrationConsole.png
   :alt: Migrate configuration on console
   :class: with-border

   Migrate configuration on console

.. note::
   After executing the upgrade wizard you have to flush the cache via the
   module :guilabel:`Admin Tools` > :guilabel:`Maintenance`.

The migration wizard updates:

* File :file:`config/<site_identifier>/config.yaml`


From version 0.2 to 0.3
=======================

To allow the configuration of more than one Matomo instance the configuration
moved from the extension configuration to the :ref:`site management
<site-configuration>`.

.. attention::
   As with all upgrades: Please backup your data before executing the upgrade
   wizard!

.. note::
   If only one site is available the migration of the configuration can be done
   with a upgrade wizard. If there is more than one site configured you have to
   migrate the configuration by yourself. For this purpose the extension
   configuration is still available but has no effect at all.


Migrating from extension configuration to site configuration
------------------------------------------------------------

The migration can be started in the TYPO3 backend via :guilabel:`Admin Tools` >
:guilabel:`Upgrade` > :guilabel:`Upgrade Wizard`.

.. figure:: /Images/SiteConfigurationMigrationBackend.png
   :alt: Migrate configuration in backend
   :class: with-border

   Migrate configuration in backend

Alternatively, you can also execute the migration wizard on a TYPO3 console:

.. figure:: /Images/SiteConfigurationMigrationConsole.png
   :alt: Migrate configuration on console
   :class: with-border

   Migrate configuration on console

.. note::
   After executing the upgrade wizard you have to flush the cache via the
   module :guilabel:`Admin Tools` > :guilabel:`Maintenance`.

The migration wizard updates:

* File :file:`typo3conf/LocalConfiguration.php`
* File :file:`config/<site_identifier>/config.yaml`

.. hint::
   If you use Git for versioning your site configuration you should consider
   to store the authentication token in an
   :ref:`environment variable <t3coreapi:sitehandling-using-env-vars>` for
   better security.


Migrating the dashboard widgets
-------------------------------

As the identifiers of the dashboard widgets have changed they can also be
migrated to the new identifiers. If multiple site configuration exist
the widgets have to be assigned manually to the dashboards again.

The migration can be started in the TYPO3 backend via :guilabel:`Admin Tools` >
:guilabel:`Upgrade` > :guilabel:`Upgrade Wizard`.

.. figure:: /Images/WidgetMigrationBackend.png
   :alt: Migrate dashboard widgets in backend
   :class: with-border

   Migrate dashboard widgets in backend

Alternatively, you can also execute the migration wizard on a TYPO3 console:

.. figure:: /Images/WidgetMigrationConsole.png
   :alt: Migrate dashboard widgets on console
   :class: with-border

   Migrate dashboard widgets on console

.. note::
   After executing the upgrade wizard you have to flush the cache via the
   module :guilabel:`Admin Tools` > :guilabel:`Maintenance`.

The migration wizard updates:

* Table "be_dashboards"


Migrating the backend user group configuration
----------------------------------------------

Use this upgrade wizard to migrate the widget identifiers to the new
format. If multiple site configuration exist the widgets have to be assigned
manually to the backend user groups again.

.. figure:: /Images/WidgetBackendUserGroupMigrationBackend.png
   :alt: Migrate widgets identifiers of backend user groups in backend
   :class: with-border

   Migrate widgets identifiers of backend user groups in backend

Alternatively, you can also execute the migration wizard on a TYPO3 console:

.. figure:: /Images/WidgetBackendUserGroupMigrationConsole.png
   :alt: Migrate widgets identifiers of backend user groups on console
   :class: with-border

   Migrate widgets identifiers of backend user groups on console

The migration wizard updates:

* Table "be_groups"

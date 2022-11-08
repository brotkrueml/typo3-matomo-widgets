.. include:: /Includes.rst.txt

.. index:: Configuration

.. _configuration:

=============
Configuration
=============

Target group: **Developers, Integrators**

.. contents:: Table of Contents
   :depth: 1
   :local:


.. _site-configuration:

Site configuration
==================

To configure the extension, go to :guilabel:`Site Management` > :guilabel:`Sites`
and select the appropriate site configuration. Click on the :guilabel:`Matomo
Widgets` tab:

.. figure:: /Images/SiteManagement.png
   :alt: Options in the site management

   Options in the site management

.. attention::
   Due to technical restrictions it is not possible to import the Matomo
   Widgets configuration from another file. It has to be stored in the site
   configuration's :file:`config.yaml`.

.. important::
   If you adjust settings for the Matomo widgets in the site configuration you
   have to flush the cache via :guilabel:`Admin Tools` > :guilabel:`Maintenance`
   or in TYPO3 v11+ on console with :bash:`vendor/bin/typo3 cache:flush`.

.. note::
   A Matomo instance is only connected to a site if a base URL and a site ID
   are defined.

Title
   The title will prefix the widget title. You can leave it empty if you only
   have one site. But you should define a title when connecting multiple Matomo
   instances with your TYPO3 installation to differentiate them in the
   dashboard.

Consider configuration from Matomo Integration extension
   This field is only displayed with installed and activated
   :ref:`Matomo Integration <ext_matomo_integration:introduction>` extension.
   Enable this option to use common configuration (like base URL and site ID)
   from the "Matomo Integration" extension. In this case the common
   configuration is hidden from this tab.

Base URL
   Enter the URL of your Matomo instance. This field is only available when
   :guilabel:`Consider configuration from Matomo Integration extension` is
   deactivated.

   .. important::
      Please ensure TLS (https) is used for connecting to the Matomo
      installation as the authentication token is transferred in plain text!

Site ID
   Enter the site id for the website. This field is only available when
   :guilabel:`Consider configuration from Matomo Integration extension` is
   deactivated.

Authentication token
   Enter the authentication token (token_auth).

   See the Matomo documentation for how to `generate a token_auth`_.

   .. important::
      It is recommended to create an own user in Matomo which has only read
      access to the given site. If you use Git for versioning your site
      configuration you should consider to store the authentication token in an
      :ref:`environment variable <t3coreapi:sitehandling-using-env-vars>` for
      better security.

Active Widgets
   You can activate or deactivate each available :ref:`widget <widgets>` for a
   site. Deactivated widgets cannot be selected in the dashboard.

Pages Not Found Template
   .. versionadded:: 1.3.0

   Enter the template to specify the page title for pages which are not found.
   See `How to track error pages in Matomo`_ for more information. Please use
   the placeholders `{path}` for the path/URL and `{referrer}` for the referrer.
   This field is only available when :guilabel:`Consider configuration from
   Matomo Integration extension` is deactivated.

   Default: *404/URL = {path}/From = {referrer}*

.. _configuration-keys:

Configuration keys
==================

The values from the :guilabel:`Sites` module are stored in the according YAML
file. Following is a list of the possible keys and values.

.. confval:: matomoWidgetsActiveWidgets

   :type: string
   :Default: ''

   Comma-delimited list of the widget names. You can find the according widget
   value in the :ref:`widget overview <widgets>` as "Active widgets value".

.. confval:: matomoWidgetsConsiderMatomoIntegration

   :type: bool
   :Default: false

   With installed :doc:`Matomo Integration <ext_matomo_integration:Index>`
   extension the URL and the site ID are taken from the configuration of that
   extension.

.. confval:: matomoWidgetsCustomDimensions

   :type: array
   :Default: []

   Configuration for custom dimensions,
   see :ref:`configuring-custom-dimensions`.

.. confval:: matomoWidgetsIdSite

   :type: int
   :Default: ''

   The site ID to track into.

.. confval:: matomoWidgetsPagesNotFoundTemplate

   .. versionadded:: 1.3.0

   :type: string
   :Default: '404/URL = {path}/From = {referrer}'

   The template for a 404 page, see `How to track error pages in Matomo`_ for
   more information.

.. confval:: matomoWidgetsTitle

   :type: string
   :Default: ''

   The widgets are prefixed with this title.

.. confval:: matomoWidgetsTokenAuth

   :type: string
   :Default: ''

   The authorisation token pro retrieving the data via the API.

.. confval:: matomoWidgetsUrl

   :type: string
   :Default: ''

   The URL of the Matomo installation.


.. _configuring-custom-dimensions:

Custom dimensions
=================

Custom dimensions cannot be configured via the :guilabel:`Site Management`
module as there is currently no possibility to add IRRE elements to a site
configuration by an extension. So, custom dimensions have to be configured
manually in the :file:`config/sites/*/config.yml` file:

.. code-block:: yaml
   :caption: config/sites/your_site/config.yml
   :linenos:
   :emphasize-lines: 2-12

   matomoWidgetsActiveWidgets: 'actionsPerDay,actionsPerMonth'
   matomoWidgetsCustomDimensions:
     -
       scope: 'visit'
       idDimension: 1
       title: 'User Type'
       description: 'Displays the custom dimension for the user type'
     -
       scope: 'action'
       idDimension: 4
       title: 'Page Location'
       description: 'Display the custom dimension for the page location'
   matomoWidgetsIdSite: 1
   matomoWidgetsTitle: ''
   matomoWidgetsTokenAuth: ''
   matomoWidgetsUrl: 'https://demo.matomo.cloud/'

You begin the configuration for a custom dimension with the key
:yaml:`matomoWidgetsCustomDimensions` as shown in line 2 of the example. It is
followed by an array which describes the custom dimensions:

scope (required)
   The scope can be :yaml:`action` or :yaml:`visit`.

idDimension (required)
   The id of the custom dimension as given in the Matomo configuration.

title (optional)
   Give a meaningful title for the custom dimension. If it is left out or empty,
   the title will be ``Custom Dimension <idDimension>``. You can also use a
   localisation string starting with ``LLL:``. The title is shown as the widget
   title and in the :guilabel:`Add widget` modal.

description (optional)
   The description is used in the :guilabel:`Add widget` modal. If it is left
   out or empty, it is not used. You can also use a localisation string starting
   with ``LLL:``.

.. note::
   You can add as many custom dimensions as you want. Configured custom
   dimensions are always active and cannot be deactivated unless they are
   removed from the configuration.


Configuration independent from a site
=====================================

.. versionadded:: 1.7.0

It is possible to add widgets for one or more Matomo site IDs independently of
a site configuration. YAML files in the :file:`config/matomo_widgets/` folder
are included at build time in addition to the sites. Add a YAML file for each
Matomo site, for instance :file:`demo.yaml`. The :ref:`configuration keys
<configuration-keys>` can be used, such as:

.. code-block:: yaml
   :caption: config/matomo_widgets/demo.yaml

   matomoWidgetsActiveWidgets: 'actionsPerDay,bounceRate,visitsPerDays'
   matomoWidgetsIdSite: 1
   matomoWidgetsTitle: 'Demo'
   matomoWidgetsUrl: 'https://demo.matomo.cloud/'


Permission of widgets
=====================

You have to grant access for editors to use some or all of the Matomo widgets.
You can find more information in the :ref:`Dashboard manual
<t3dashboard:permission-handling-of-widgets>`.

.. note::
   Only activated widgets for a site are available. The widget titles are always
   English if more than one Matomo instance is configured.


Cache configuration
===================

The extension stores the data retrieved from the Matomo instance in a cache
for better performance. You can adjust the cache configuration, have a look
into :file:`ext_localconf.php` of this extension for the current configuration.


.. _widget-configuration:

Widget configuration
====================

The widgets provided by this extension are a starting point. You can adjust some
parameters sent to the Matomo instance for each widget. An example would be
to raise the displayed number of days for the *Visits per month* widget from
12 months to 24 months.

**Example:**

.. code-block:: yaml
   :caption: EXT:your_site_package/Configuration/Services.yaml

   parameters:
      matomo_widgets.visitsPerMonth.parameters:
         period: 'month'
         date: 'last24'  # default value: 'last12'

:yaml:`period` and :yaml:`date` are parameters from the Matomo Reporting API.
There are some more that might be interesting for you.

.. tip::
   Have a look into the `Matomo Reporting API`_ for a reference of the available
   Matomo parameters and values.

You can find all configuration parameters in the chapter :ref:`widgets`.

.. hint::
   Please provide all necessary parameters for the Matomo API, otherwise you
   will get an error. This means that even if you only overwrite the
   :yaml:`date` parameter, you must specify the :yaml:`period` parameter even
   though it has not changed.


.. _generate a token_auth: https://matomo.org/faq/general/faq_114/
.. _How to track error pages in Matomo: https://matomo.org/faq/how-to/faq_60/
.. _Matomo Reporting API: https://developer.matomo.org/api-reference/reporting-api

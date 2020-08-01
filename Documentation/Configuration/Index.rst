.. include:: ../Includes.txt

.. index:: Configuration

.. _configuration:

=============
Configuration
=============

Target group: **Developers, Integrators**

.. contents:: Table of Contents
   :depth: 1
   :local:


.. _extension-configuration:

Extension configuration
=======================

To configure the extension, go to :guilabel:`Admin Tools` > :guilabel:`Settings`
> :guilabel:`Extension Configuration` and click on the
:guilabel:`Configure extensions` button. Open the :guilabel:`matomo_widgets`
configuration:

.. figure:: ../Images/ExtensionConfiguration.png
   :alt: Options in the extension configuration

   Options in the extension configuration


Base URL of the Matomo installation
   Enter the URL of your Matomo installation.

   .. important::
      Please ensure TLS (https) is used for connecting to the Matomo
      installation as the authentication token is transferred in plain text!

Site ID
   Enter the site id for the website.

Authentication token
   Enter the authentication token (token_auth).

   To retrieve a authentication token navigate in your Matomo installation to
   the administration area and click on :guilabel:`Personal` >
   :guilabel:`Settings`. There you'll find a section :guilabel:`API
   Authentication Token`.

   .. important::
      It is recommended to create an own user in Matomo which has only read
      access to this site.


Permission of widgets
=====================

You have to grant access for editors to use some or all of the Matomo widgets.
You can find more information in the :ref:`Dashboard manual
<t3dashboard:permission-handling-of-widgets>`.


Cache configuration
===================

The extension stores the data retrieved from the Matomo installation in a cache
for better performance. You can adjust the cache configuration, have a look
into :file:`ext_localconf.php` for the current configuration.


.. _widget-configuration:

Widget configuration
====================

.. caution::
   The extension is in beta status. That means it still has some incomplete
   features and bugs, and at some point – sooner or later – it will probably
   break! Upgrading to subsequent versions will be possible, but might involve
   getting your hands dirty.

The widgets provided by this extension are a starting point. You can adjust the
parameters sent to the Matomo installation for each delivered widget.

The parameters are defined at the beginning of the
:file:`Configuration/Services.yaml` file. You can overwrite them in your own
services file, for example in your site package extension.

The data providers for the widgets and the widgets themselves are configured in
the :file:`Configuration/Services.yaml` file. Each data provider has a
:yaml:`parameters` argument which - well - defines the arguments with which the
Matomo API is called. You can adjust these parameters to your needs, e.g. to
change the number of months displayed in the *Visits per month* widget from 12
to 24. Also have a look into the :ref:`Dashboard manual
<t3dashboard:introduction>` which describes the configuration of widgets and
data providers in general.

**Example:**

.. code-block:: yaml

   parameters:
      matomo_widgets.visitsPerMonth.parameters:
         period: 'month'
         date: 'last24'  # default value: 'last12'

:yaml:`period` and :yaml:`date` are parameters from the Matomo Reporting API.
There are some more that might be interesting for you.

.. tip::
   Have a look into the `Matomo Reporting API
   <https://developer.matomo.org/api-reference/reporting-api>`_ for a reference
   of the available Matomo parameters and values.

You can find all configuration parameters in the chapter :ref:`widgets` or in
the :file:`Configuration/Services.yaml` file shipped with this extension.

.. hint::
   Please provide all necessary parameters for the Matomo API, otherwise you
   will get an error. This means that even if you only overwrite the
   :yaml:`date` parameter, you must specify the :yaml:`period` parameter even
   though it has not changed.

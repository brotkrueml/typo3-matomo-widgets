.. include:: /Includes.rst.txt

.. _widgets:

=======
Widgets
=======

You can add the Matomo widgets like any other widget to a dashboard. Please
note, that the widgets have to be enabled in the :ref:`site configuration
<site-configuration>` and the permissions for editors have to be granted to be able
to use these widgets.

.. hint::
   Most widgets need an activated module in Matomo to work. If a module is not
   available, an error is displayed in the widget. The parameters of the
   underlying data providers can be adjusted. Have a look into the
   :ref:`widget-configuration` section.

The following widgets are available. A link to the corresponding page in Matomo
is displayed via in icon in the header bar of the widget if the report is
available.

.. contents::
   :depth: 1
   :local:


.. tip::
   Do you miss a widget? Open a `feature request
   <https://github.com/brotkrueml/typo3-matomo-widgets/issues>`_ and perhaps the
   widget is available in the next release.

Actions per day
===============

This widget displays the number of actions per day for the last 28 days
(including the current day). Actions are:

- Page views
- Downloads
- Clicks on outlinks

.. figure:: /Images/WidgetActionsPerDay.png
   :alt: Widget Actions per day
   :class: with-border

   Widget *Actions per day*

Matomo module
   VisitsSummary

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.actionsPerDay.parameters
      - period: day
      - date: last28


Actions per month
=================

This widget displays the number of actions per month for the last 12 months
(including the current month). Actions are:

- Page views
- Downloads
- Clicks on outlinks

.. figure:: /Images/WidgetActionsPerMonth.png
   :alt: Widget Actions per month
   :class: with-border

   Widget *Actions per month*

Matomo module
   VisitsSummary

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.actionsPerMonth.parameters
      - period: month
      - date: last12


Bounce rate
===========

The bounce rate for the current month shows this widget:

.. figure:: /Images/WidgetBounceRate.png
   :alt: Widget Bounce Rate
   :class: with-border

   Widget *Bounce rate*

Matomo module
   VisitsSummary

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.bounceRate.parameters
      - period: month
      - date: today
   matomo_widgets.bounceRate.subtitle
      in the current month



Browser plugins
===============

This report shows which browser plugins your visitors had enabled:

.. figure:: /Images/WidgetBrowserPlugins.png
   :alt: Widget Browser Plugins
   :class: with-border

   Widget *Browser Plugins*

Matomo module
   DevicesDetection

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.browserPlugins.parameters
      - period: month
      - date: today
      - filter_limit: 50
      - filter_sort_column: nb_visits
      - filter_sort_order: desc



Browsers
========

The browser share for the current month shows this widget:

.. figure:: /Images/WidgetBrowsers.png
   :alt: Widget Browsers
   :class: with-border

   Widget *Browsers*

Matomo module
   DevicesDetection

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.browsers.limit
      5
   matomo_widgets.browsers.parameters
      - period: month
      - date: today
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


Campaigns
=========

Displays a report of the campaigns for the current month:

.. figure:: /Images/WidgetCampaigns.png
   :alt: Widget Campaigns
   :class: with-border

   Widget *Campaigns*

Matomo module
   Referrers

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.campaigns.parameters:
      - period: month
      - date: today
      - filter_limit: 30
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


Countries
=========

Shows a list of countries from which the website was visited in the current
month:

.. figure:: /Images/WidgetCountries.png
   :alt: Widget Countries
   :class: with-border

   Widget *Countries*

Matomo module
   UserCountry

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.country.parameters:
      - period: month
      - date: today
      - filter_limit: 50
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


Link to Matomo
==============

A call-to-action widget is used to show a link to the configured Matomo
installation:

.. figure:: /Images/WidgetLinkToMatomo.png
   :alt: Widget Link to Matomo
   :class: with-border

   Widget *Link to Matomo*


Operating system families
=========================

The operating system families used by the visitors for the current month:

.. figure:: /Images/WidgetOsFamilies.png
   :alt: Widget Operating system families
   :class: with-border

   Widget *Operating system families*

Matomo module
   DevicesDetection

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.osFamilies.limit:
      5
   matomo_widgets.osFamilies.parameters:
      - period: month
      - date: today
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


Visits per day
==============

This widget displays the number of visits per day for the last 28 days:

.. figure:: /Images/WidgetVisitsPerDay.png
   :alt: Widget Visits per day
   :class: with-border

   Widget *Visits per day*

Matomo module
   VisitsSummary

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.visitsPerDay.parameters:
      - period: day
      - Date: last28


Visits per month
================

This widget displays the number of visits per month for the last 12 months:

.. figure:: /Images/WidgetVisitsPerMonth.png
   :alt: Widget Visits per month
   :class: with-border

   Widget *Visits per month*

Matomo module
   VisitsSummary

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.visitsPerMonth.parameters:
      - period: month
      - Date: last12

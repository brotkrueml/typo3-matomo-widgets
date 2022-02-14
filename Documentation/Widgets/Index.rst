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
   Do you miss a widget? Open a `feature request`_ and with a little luck the
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


Annotations
===========

.. versionadded:: 1.2.0

Display a list of the recent `annotations`_ in the last 365 days:

.. figure:: /Images/WidgetAnnotations.png
   :alt: Widget Annotations
   :class: with-border

   Widget *Annotations*

Matomo module
   Annotations

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.annotations.parameters
      - period: 'month'
      - date: 'today'
      - lastN: 365


Create annotation
=================

.. versionadded:: 1.3.0

This widget provides a form to create an `annotation`_ conveniently from the
TYPO3 backend:

.. figure:: /Images/WidgetCreateAnnotation.png
   :alt: Widget Create annotation
   :class: with-border

   Widget *Create annotation*

After submitting the form, a notification is displayed in the upper right corner
of the browser window. If the creation of the annotation was successful, the
:guilabel:`Note` field will be cleared.

Matomo module
   Annotations

.. note::
   The annotation is stored in Matomo under the user configured in the
   :ref:`site configuration <site-configuration>`. A log entry has been created
   that can be viewed in the :guilabel:`System` > :guilabel:`Log` module.
   In addition to the user, the date, the note and the note ID received are
   saved:

   .. figure:: /Images/AdministrationLogEntry.png
      :alt: Entry in the administration log
      :class: with-border

      Entry in the administration log

.. note::
   For the creation of an annotation the according Matomo user needs only the
   `view` permission.


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
   DevicePlugins

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


Content names
=============

The content name report is part of `content tracking`_. The widget displays the
names, impressions and interaction rate of the content the visitors viewed and
interacted with.

.. figure:: /Images/WidgetContentNames.png
   :alt: Widget Content Names
   :class: with-border

   Widget *Content Names*

Matomo module
   Contents

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.campaigns.parameters:
      - period: month
      - date: today
      - filter_limit: 50
      - filter_sort_column: nb_impressions
      - filter_sort_order: desc


Content pieces
==============

The content piece report is part of `content tracking`_. The widget displays the
pieces, impressions and interaction rate of the content the visitors viewed and
interacted with.

.. figure:: /Images/WidgetContentPieces.png
   :alt: Widget Content Pieces
   :class: with-border

   Widget *Content Pieces*

Matomo module
   Contents

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.campaigns.parameters:
      - period: month
      - date: today
      - filter_limit: 50
      - filter_sort_column: nb_impressions
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


Custom dimensions
=================

With `custom dimensions`_ any custom data can be assigned to visitors or
actions. Before a custom dimension can be used as a dashboard widget it has to
be :ref:`configured for a site <configuring-custom-dimensions>`.

The columns for a custom dimension widget depend on the scope of the custom
dimension.

Matomo module
   CustomDimensions

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.customDimension<idDimension>.parameters:
      - period: month
      - date: today
      - filter_limit: 50
      - filter_sort_column: nb_visits
      - filter_sort_order: desc

The ``<idDimension>`` is the id of the custom dimension as available in the
`Matomo configuration <https://matomo.org/docs/custom-dimensions/#creating-custom-dimensions>`_.


Scope "action"
--------------

.. figure:: /Images/WidgetCustomDimensionAction.png
   :alt: Example widget for a custom dimension with scope "action"
   :class: with-border

   Example widget for a custom dimension with scope "action"

Scope "visit"
-------------

.. figure:: /Images/WidgetCustomDimensionVisit.png
   :alt: Example widget for a custom dimension with scope "visit"
   :class: with-border

   Example widget for a custom dimension with scope "visit"


JavaScript errors
=================

.. versionadded:: 1.3.0

Displays a list of JavaScript errors that occurred in the last 14 days:

.. figure:: /Images/WidgetJavaScriptErrors.png
   :alt: Widget JavaScript errors
   :class: with-border

   Widget *JavaScript errors*

Clicking on the message opens a modal with details to this message:

.. figure:: /Images/WidgetJavaScriptErrorsDetailsModal.png
   :alt: Modal with details for a specific error
   :class: with-border

   Modal with details for a specific error


Matomo module
   Events

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.javaScriptErrors.parameters:
      - period: range
      - date: last14
      - filter_limit: 50
      - filter_sort_column: nb_events
      - filter_sort_order: desc

.. note::
   To use this widget, JavaScript error tracking must be activated. This can be
   done either by adding the `enableJSErrorTracking`_ code or by enabling the
   corresponding option in the installed :ref:`Matomo Integration extension
   <matomo_integration:site-configuration>`.


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


Pages not found
===============

.. versionadded:: 1.3.0

List of pages in the last 28 days which returned a status code 404 (not found):

.. figure:: /Images/WidgetPagesNotFound.png
   :alt: Widget Pages not found
   :class: with-border

   Widget *Pages not found*

Matomo module
   Actions

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.siteSearchKeywords.parameters:
      - period: range
      - date: last28
      - filter_limit: 50
      - filter_sort_column: nb_hits
      - filter_sort_order: desc


Site search keywords
====================

Overview of the search keywords that visitors searched for on the internal
search engine:

.. figure:: /Images/WidgetSiteSearchKeywords.png
   :alt: Widget Site search keywords
   :class: with-border

   Widget *Site search keywords*

Matomo module
   Actions

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.siteSearchKeywords.parameters:
      - period: month
      - date: today
      - filter_limit: 50
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


Site search keywords with no result
===================================

List of the site search keywords that did not return any search result:

.. figure:: /Images/WidgetSiteSearchNoResultKeywords.png
   :alt: Widget Site search keywords with no result
   :class: with-border

   Widget *Site search keywords with no result*

Matomo module
   Actions

Default configuration parameters in the :file:`Services.yaml` file
   matomo_widgets.siteSearchNoResultKeywords.parameters:
      - period: month
      - date: today
      - filter_limit: 50
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
      - date: last28


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
      - date: last12


.. _annotation: https://matomo.org/docs/annotations/
.. _annotations: https://matomo.org/docs/annotations/
.. _content tracking: https://matomo.org/docs/content-tracking/
.. _custom dimensions: https://matomo.org/docs/custom-dimensions/
.. _enableJSErrorTracking: https://matomo.org/faq/how-to/how-do-i-enable-basic-javascript-error-tracking-and-reporting-in-matomo-browser-console-error-messages/
.. _feature request: https://github.com/brotkrueml/typo3-matomo-widgets/issues

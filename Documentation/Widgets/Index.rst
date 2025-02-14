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

   Widget *Actions per day*

Matomo module
   VisitsSummary

Active widgets value in :file:`config.yaml`
   actionsPerDay

Default configuration parameters in the :file:`Configuration/Services.yaml` file
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

   Widget *Actions per month*

Matomo module
   VisitsSummary

Active widgets value in :file:`config.yaml`
   actionsPerMonth

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.actionsPerMonth.parameters
      - period: month
      - date: last12


Annotations
===========

Display a list of the recent `annotations`_ in the last 365 days:

.. figure:: /Images/WidgetAnnotations.png
   :alt: Widget Annotations

   Widget *Annotations*

Matomo module
   Annotations

Active widgets value in :file:`config.yaml`
   annotations

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.annotations.parameters
      - period: 'month'
      - date: 'today'
      - lastN: 365


Create annotation
=================

This widget provides a form to create an `annotation`_ conveniently from the
TYPO3 backend:

.. figure:: /Images/WidgetCreateAnnotation.png
   :alt: Widget Create annotation

   Widget *Create annotation*

After submitting the form, a notification is displayed in the upper right corner
of the browser window. If the creation of the annotation was successful, the
:guilabel:`Note` field will be cleared.

Matomo module
   Annotations

Active widgets value in :file:`config.yaml`
   createAnnotation

.. note::
   The annotation is stored in Matomo under the user configured in the
   :ref:`site configuration <site-configuration>`. A log entry has been created
   that can be viewed in the :guilabel:`System` > :guilabel:`Log` module.
   In addition to the user, the date, the note and the note ID received are
   saved:

   .. figure:: /Images/AdministrationLogEntry.png
      :alt: Entry in the administration log

      Entry in the administration log

.. note::
   For the creation of an annotation the according Matomo user needs only the
   `view` permission.


.. _widgets-bounce-rate:

Bounce rate
===========

.. versionchanged:: 3.0
   The bounce rate for the last 28 days is displayed. In older versions the
   rate for the current month has been shown.

The bounce rate for the last 28 days shows this widget:

.. figure:: /Images/WidgetBounceRate.png
   :alt: Widget Bounce Rate

   Widget *Bounce rate*

Matomo module
   VisitsSummary

Active widgets value in :file:`config.yaml`
   bounceRate

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.bounceRate.parameters
      - period: range
      - date: last28
   matomo_widgets.bounceRate.subtitle
      in the last 28 days (incl. today)


.. _widgets-browser-plugins:

Browser plugins
===============

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

This report shows which browser plugins your visitors had enabled in the last
28 days:

.. figure:: /Images/WidgetBrowserPlugins.png
   :alt: Widget Browser Plugins

   Widget *Browser Plugins*

Matomo module
   DevicePlugins

Active widgets value in :file:`config.yaml`
   browserPlugins

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.browserPlugins.parameters
      - period: range
      - date: last28
      - filter_limit: 50
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


.. _widgets-browsers:

Browsers
========

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

The browser share for the last 28 days shows this widget:

.. figure:: /Images/WidgetBrowsers.png
   :alt: Widget Browsers

   Widget *Browsers*

Matomo module
   DevicesDetection

Active widgets value in :file:`config.yaml`
   browsers

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.browsers.limit
      5
   matomo_widgets.browsers.parameters
      - period: range
      - date: last28
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


.. _widgets-campaigns:

Campaigns
=========

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

Displays a report of the campaigns for the last 28 days:

.. figure:: /Images/WidgetCampaigns.png
   :alt: Widget Campaigns

   Widget *Campaigns*

Matomo module
   Referrers

Active widgets value in :file:`config.yaml`
   campaigns

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.campaigns.parameters:
      - period: range
      - date: last28
      - filter_limit: 30
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


.. _widgets-content-names:

Content names
=============

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

The content name report is part of `content tracking`_. The widget displays the
names, impressions and interaction rate of the content the visitors viewed and
interacted with.

.. figure:: /Images/WidgetContentNames.png
   :alt: Widget Content Names

   Widget *Content Names*

Matomo module
   Contents

Active widgets value in :file:`config.yaml`
   contentNames

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.campaigns.parameters:
      - period: range
      - date: last28
      - filter_limit: 50
      - filter_sort_column: nb_impressions
      - filter_sort_order: desc


.. _widgets-content-pieces:

Content pieces
==============

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

The content piece report is part of `content tracking`_. The widget displays the
pieces, impressions and interaction rate of the content the visitors viewed and
interacted with.

.. figure:: /Images/WidgetContentPieces.png
   :alt: Widget Content Pieces

   Widget *Content Pieces*

Matomo module
   Contents

Active widgets value in :file:`config.yaml`
   contentPieces

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.campaigns.parameters:
      - period: range
      - date: last28
      - filter_limit: 50
      - filter_sort_column: nb_impressions
      - filter_sort_order: desc


.. _widgets-countries:

Countries
=========

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

Shows a list of countries from which the website was visited in the last 28
days:

.. figure:: /Images/WidgetCountries.png
   :alt: Widget Countries

   Widget *Countries*

Matomo module
   UserCountry

Active widgets value in :file:`config.yaml`
   countries

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.country.parameters:
      - period: range
      - date: last28
      - filter_limit: 50
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


.. _widgets-custom-dimensions:

Custom dimensions
=================

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

With `custom dimensions`_ any custom data can be assigned to visitors or
actions. Before a custom dimension can be used as a dashboard widget it has to
be :ref:`configured for a site <configuring-custom-dimensions>`.

The columns for a custom dimension widget depend on the scope of the custom
dimension.

Matomo module
   CustomDimensions

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.customDimension<idDimension>.parameters:
      - period: range
      - date: last28
      - filter_limit: 50
      - filter_sort_column: nb_visits
      - filter_sort_order: desc

The ``<idDimension>`` is the ID of the custom dimension as available in the
`Matomo configuration <https://matomo.org/docs/custom-dimensions/#creating-custom-dimensions>`_.


Scope "action"
--------------

.. figure:: /Images/WidgetCustomDimensionAction.png
   :alt: Example widget for a custom dimension with scope "action"

   Example widget for a custom dimension with scope "action"

Scope "visit"
-------------

.. figure:: /Images/WidgetCustomDimensionVisit.png
   :alt: Example widget for a custom dimension with scope "visit"

   Example widget for a custom dimension with scope "visit"


JavaScript errors
=================

Displays a list of JavaScript errors that occurred in the last 14 days:

.. figure:: /Images/WidgetJavaScriptErrors.png
   :alt: Widget JavaScript errors

   Widget *JavaScript errors*

Clicking on the message opens a modal with details to this message:

.. figure:: /Images/WidgetJavaScriptErrorsDetailsModal.png
   :alt: Modal with details for a specific error

   Modal with details for a specific error


Matomo module
   Events

Active widgets value in :file:`config.yaml`
   javaScriptErrors

Default configuration parameters in the :file:`Configuration/Services.yaml` file
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
   <ext_matomo_integration:site-configuration>`.


Link to Matomo
==============

A call-to-action widget is used to show a link to the configured Matomo
installation:

.. figure:: /Images/WidgetLinkToMatomo.png
   :alt: Widget Link to Matomo

   Widget *Link to Matomo*

Active widgets value in :file:`config.yaml`
   linkMatomo


.. _widgets-most-views-pages:

Most viewed pages
=================

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

Show the most viewed pages of a site for the last 28 days:

.. figure:: /Images/WidgetMostViewedPages.png
   :alt: Widget Most viewed pages

   Widget *Most viewed pages*

Matomo module
   Actions

Active widgets value in :file:`config.yaml`
   mostViewedPages

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.mostViewedPages.parameters:
      - period: range
      - date: last28
      - filter_sort_column: nb_hits
      - filter_sort_order: desc

.. tip::
   When you see URLs with the addition "- Others" then Matomo groups pages
   automatically after a given limit: You can `increase the limit`_. Maybe you
   want then `invalidate the historical reports`_ to adopt the change.


.. _widgets-operating-system-families:

Operating system families
=========================

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

The operating system families used by the visitors for the last 28 days:

.. figure:: /Images/WidgetOsFamilies.png
   :alt: Widget Operating system families

   Widget *Operating system families*

Matomo module
   DevicesDetection

Active widgets value in :file:`config.yaml`
   osFamilies

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.osFamilies.limit:
      5
   matomo_widgets.osFamilies.parameters:
      - period: range
      - date: last28
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


Pages not found
===============

List of pages in the last 28 days which returned a status code 404 (not found):

.. figure:: /Images/WidgetPagesNotFound.png
   :alt: Widget Pages not found

   Widget *Pages not found*

Matomo module
   Actions

Active widgets value in :file:`config.yaml`
   pagesNotFound

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.siteSearchKeywords.parameters:
      - period: range
      - date: last28
      - filter_limit: 50
      - filter_sort_column: nb_hits
      - filter_sort_order: desc


.. _widgets-operating-site-search-keywords:

Site search keywords
====================

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

Overview of the search keywords that visitors searched for on the internal
search engine:

.. figure:: /Images/WidgetSiteSearchKeywords.png
   :alt: Widget Site search keywords

   Widget *Site search keywords*

Matomo module
   Actions

Active widgets value in :file:`config.yaml`
   siteSearchKeywords

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.siteSearchKeywords.parameters:
      - period: range
      - date: last28
      - filter_limit: 50
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


.. _widgets-operating-site-search-keywords-no-result:

Site search keywords with no result
===================================

.. versionchanged:: 3.0
   The numbers for the last 28 days are displayed. In older versions the numbers
   for the current month have been shown.

List of the site search keywords that did not return any search result:

.. figure:: /Images/WidgetSiteSearchNoResultKeywords.png
   :alt: Widget Site search keywords with no result

   Widget *Site search keywords with no result*

Matomo module
   Actions

Active widgets value in :file:`config.yaml`
   siteSearchNoResultKeywords

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.siteSearchNoResultKeywords.parameters:
      - period: range
      - date: last28
      - filter_limit: 50
      - filter_sort_column: nb_visits
      - filter_sort_order: desc


Visits per day
==============

This widget displays the number of visits per day for the last 28 days:

.. figure:: /Images/WidgetVisitsPerDay.png
   :alt: Widget Visits per day

   Widget *Visits per day*

Matomo module
   VisitsSummary

Active widgets value in :file:`config.yaml`
   visitsPerDay

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.visitsPerDay.parameters:
      - period: day
      - date: last28


Visits per month
================

This widget displays the number of visits per month for the last 12 months:

.. figure:: /Images/WidgetVisitsPerMonth.png
   :alt: Widget Visits per month

   Widget *Visits per month*

Matomo module
   VisitsSummary

Active widgets value in :file:`config.yaml`
   visitsPerMonth

Default configuration parameters in the :file:`Configuration/Services.yaml` file
   matomo_widgets.visitsPerMonth.parameters:
      - period: month
      - date: last12


.. _annotation: https://matomo.org/docs/annotations/
.. _annotations: https://matomo.org/docs/annotations/
.. _content tracking: https://matomo.org/docs/content-tracking/
.. _custom dimensions: https://matomo.org/docs/custom-dimensions/
.. _enableJSErrorTracking: https://matomo.org/faq/how-to/how-do-i-enable-basic-javascript-error-tracking-and-reporting-in-matomo-browser-console-error-messages/
.. _feature request: https://github.com/brotkrueml/typo3-matomo-widgets/issues
.. _increase the limit: https://matomo.org/faq/how-to/faq_54/
.. _invalidate the historical reports: https://matomo.org/faq/how-to/faq_155/

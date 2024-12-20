.. include:: /Includes.rst.txt

.. _introduction:

============
Introduction
============

.. contents:: Table of Contents
   :depth: 2
   :local:

.. _what-it-does:

What does it do?
================

The extension provides charts for various `Matomo`_ reports via the
`Reporting API`_.

The default parameters of the widgets can be adjusted to your needs, for
example, in the *Visits per month* widget display 24 month instead of 12 months.

.. tip::
   The :ref:`Matomo Integration <ext_matomo_integration:introduction>` extension
   provides an easy way to integrate Matomo into TYPO3.

.. _screenshots:

Screenshots
===========

.. figure:: /Images/DashboardExample.jpg
   :alt: Example for a Matomo Dashboard

   Example for a Matomo Dashboard


No Matomo instance at hand?
===========================

You can use the Matomo demo installation for evaluation. Just enter the
following settings into the :ref:`site configuration
<site-configuration>`:

Base URL of the Matomo instance
   https://demo.matomo.cloud/

Site ID
   1

Authentication token
   (leave empty)


.. _release-management:

Release management
==================
This extension uses `semantic versioning`_ which basically means for you, that

*  Bugfix updates (for example, 1.0.0 => 1.0.1) just includes small bug fixes or
   security relevant stuff without breaking changes.
*  Minor updates (for example, 1.0.0 => 1.1.0) includes new features and smaller
   tasks without breaking changes.
*  Major updates (for example, 1.0.0 => 2.0.0) breaking changes which can be
   refactorings, features or bug fixes.

The changes between the different versions can be found in the
:ref:`changelog <changelog>`.


.. _Matomo: https://www.matomo.org/
.. _Reporting API: https://developer.matomo.org/guides/querying-the-reporting-api
.. _semantic versioning: https://semver.org/

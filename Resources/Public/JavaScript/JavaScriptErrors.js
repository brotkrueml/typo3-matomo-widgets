require([
  'TYPO3/CMS/Core/Ajax/AjaxRequest',
  'TYPO3/CMS/Core/DocumentService',
  'TYPO3/CMS/Core/Event/RegularEvent',
  'TYPO3/CMS/Backend/Modal',
], function(AjaxRequest, DocumentService, RegularEvent, Modal) {
  'use strict';

  const LINK_SELECTOR = '.js-matomo-widgets-javascript-error-message';
  const ROUTE_KEY = 'matomo_widgets_javascript_error_details';

  const showDetails = (siteIdentifier, errorMessage) => {
    const searchParams = new URLSearchParams();
    searchParams.append('siteIdentifier', siteIdentifier);
    searchParams.append('errorMessage', errorMessage);

    const url = TYPO3.settings.ajaxUrls[ROUTE_KEY] + '&' + searchParams.toString();
    const configuration = {
      type: Modal.types.ajax,
      title: errorMessage,
      content: url,
      additionalCssClasses: ['my-dashboard-modal'],
      size: Modal.sizes.large,
    };
    Modal.advanced(configuration);
  }

  DocumentService.ready().then(() => {
    new RegularEvent('click', function (event) {
      event.preventDefault();
      const siteIdentifier = event.target.dataset.siteIdentifier;
      const errorMessage = event.target.innerText;
      showDetails(siteIdentifier, errorMessage);
    }).delegateTo(document, LINK_SELECTOR);
  });
})

import RegularEvent from '@typo3/core/event/regular-event.js';
import Modal from '@typo3/backend/modal.js';

class JavascriptErrors {
  constructor() {
    this.linkSelector = '.js-matomo-widgets-javascript-error-message';
    this.routeKey = 'matomo_widgets_javascript_error_details';
  }

  initialize() {
    new RegularEvent('click', event => {
      event.preventDefault();
      const siteIdentifier = event.target.dataset.siteIdentifier;
      const errorMessage = event.target.innerText;
      this.showDetails(siteIdentifier, errorMessage);
    }).delegateTo(document, this.linkSelector);
  }

  showDetails(siteIdentifier, errorMessage) {
    const searchParams = new URLSearchParams();
    searchParams.append('siteIdentifier', siteIdentifier);
    searchParams.append('errorMessage', errorMessage);

    const url = TYPO3.settings.ajaxUrls[this.routeKey] + '&' + searchParams.toString();
    const configuration = {
      type: Modal.types.ajax,
      title: errorMessage,
      content: url,
      additionalCssClasses: ['my-dashboard-modal'],
      size: Modal.sizes.large,
    };
    Modal.advanced(configuration);
  }
}

export default new JavascriptErrors;

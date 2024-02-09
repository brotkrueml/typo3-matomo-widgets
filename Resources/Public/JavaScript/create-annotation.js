import AjaxRequest from "@typo3/core/ajax/ajax-request.js";
import RegularEvent from '@typo3/core/event/regular-event.js';
import Notification from '@typo3/backend/notification.js';

class CreateAnnotation {
  constructor() {
    this.formSelector = 'form[data-matomowidgets-createannotation]';
    this.routeKey = 'matomo_widgets_create_annotation';
  }

  initialize() {
    new RegularEvent('submit', event => {
      event.preventDefault();
      this.sendRequest(event.target);
    }).delegateTo(document, this.formSelector);
  }

  sendRequest(formElement) {
    const dateElement = formElement.querySelector('input[name=date]');
    const noteElement = formElement.querySelector('input[name=note]');
    const siteIdentifierElement = formElement.querySelector('input[name=site_identifier]');
    const submitElement = formElement.querySelector('input[type=submit]');
    const parameters = {
      date: dateElement.value,
      note: noteElement.value,
      siteIdentifier: siteIdentifierElement.value,
    };
    const notificationTitle = formElement.dataset.notificationTitle;

    new AjaxRequest(TYPO3.settings.ajaxUrls[this.routeKey]).post(parameters).then(
      async response => {
        submitElement.disabled = true;
        const data = await response.resolve();
        if (!data.status) {
          Notification.error(notificationTitle, formElement.dataset.notificationUnknownError);
        } else if (data.status === 'success') {
          Notification.success(notificationTitle, formElement.dataset.notificationSuccess);
          noteElement.value = '';
        } else {
          Notification.error(notificationTitle, data.message);
        }
        submitElement.disabled = false;
      }, error => {
        Notification.error(notificationTitle, formElement.dataset.notificationHttpError);
        submitElement.disabled = false;
      }
    );
  }
}

export default new CreateAnnotation;

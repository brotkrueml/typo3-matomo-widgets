require([
  'TYPO3/CMS/Core/DocumentService',
  'TYPO3/CMS/Core/Event/RegularEvent',
  'TYPO3/CMS/Core/Ajax/AjaxRequest',
  'TYPO3/CMS/Backend/Notification'
], (DocumentService, RegularEvent, AjaxRequest, Notification) => {
  'use strict';

  const FORM_SELECTOR = 'form[data-matomowidgets-createannotation]';
  const ROUTE_KEY = 'matomo_widgets_create_annotation';

  const sendRequest = (formElement) => {
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

    new AjaxRequest(TYPO3.settings.ajaxUrls[ROUTE_KEY]).post(parameters).then(
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

  DocumentService.ready().then(() => {
    new RegularEvent('submit', function (event) {
      event.preventDefault();
      sendRequest(event.target);
    }).delegateTo(document, FORM_SELECTOR);
  });
});

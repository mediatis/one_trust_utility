
!function (w, d) {
    var READY_EVENT_NAME = 'one-trust-ready'
    var CONSENT_UPDATE_EVENT_NAME = 'one-trust-consent-update';
    var POLL_INTERVAL = 100;

    function checkConsent(groupId, defaultValue) {
        if (typeof defaultValue === 'undefined') {
            defaultValue = false;
        }
        if (!w.OnetrustActiveGroups) {
            return defaultValue;
        }
        return w.OnetrustActiveGroups.includes(',' + groupId + ',');
    }

    function tryToInitialize() {
        if (w.OneTrust && w.OneTrust.OnConsentChanged) {
            w.OneTrust.OnConsentChanged(function (e) {
                d.dispatchEvent(new CustomEvent(CONSENT_UPDATE_EVENT_NAME));
            });
            d.dispatchEvent(new CustomEvent(READY_EVENT_NAME));
            d.dispatchEvent(new CustomEvent(CONSENT_UPDATE_EVENT_NAME));
        } else {
            setTimeout(tryToInitialize, POLL_INTERVAL);
        }
    }
    tryToInitialize();

    w.OneTrustUtility = {
        READY_EVENT_NAME: READY_EVENT_NAME,
        CONSENT_UPDATE_EVENT_NAME: CONSENT_UPDATE_EVENT_NAME,
        checkConsent: checkConsent
    };
}(window, document);

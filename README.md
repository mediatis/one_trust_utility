OneTrust Utility
================

OneTrust Utility provides frontend and backend utilities for the consent management tool OneTrust. It is not a OneTrust integration. It just provides features if OneTrust is already in use in the current system.

Frontend Services
-----------------

If enabled, a global JavaScript object is provided, giving access to direct consent checks: `window.OneTrustUtility.checkConsent(groupId, default=false)`.

This fronent service also provides global events that can be listened to on the window object.

- `one-trust-ready`
- `one-trust-consent-update`

Backend Services
----------------

In the backend there is a new service `ConsentManager` which can be used to check for a specific consent:  `ConsentManager::checkConsent(groupId, default=false)`.

In TypoScript there is a new object available that can be used in conditions (as part of the expression language).

```
[oneTrust.checkConsent(groupId, default=false)]
    ...
[global]
```

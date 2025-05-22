# google-photos-sync

**Contributors:** wpcomspecialprojects
**Tags:**
**Requires at least:** 6.5
**Tested up to:** 6.5
**Requires PHP:** 8.3
**Stable tag:** 2.0.0
**License:** GPLv3 or later
**License URI:** http://www.gnu.org/licenses/gpl-3.0.html



## Description

> [!WARNING]
> This plugin uses Google's [`php-photoslibrary`](https://github.com/google/php-photoslibrary) package, which appears to be unmaintained and has not been updated in several years. Please evaluate the risks before using this plugin in production.

A very basic proof-of-concept plugin that enables a one-way “push” of photos from a WordPress site to a Google Photos album. The Google Photos album needs to have been created via the plugin on the WordPress site for photos to sync.

> [!NOTE]
> This plugin was initially created to be a Proof of Concept for what a two-way sync between WordPress and Google Photos would look like. Unfortunately, [Google introduced some changes to the Library API](https://developers.google.com/photos/support/updates) that made it impossible to use some of the scopes needed to implement a two-way sync.

## Installation

### Start with creating a Google App

The first thing you need to do is create a Google App for authorizing the connection between Google and your WordPress site.

Enabling the API
1. Starting at https://console.cloud.google.com/, the first step is to enable the Photos Library API by going to https://console.cloud.google.com/marketplace/product/google/photoslibrary.googleapis.com and clicking on Enable.
2. This will also create a new project on your Google account. This can be seen on the notifications (bell) icon and also in the dropdown at the top of the page.
3. While on this screen, click on Credentials then + Create Credentials and lastly OAuth Client ID.

Creating the OAuth consent screen
1. Configure an OAuth Consent Screen, by clicking on Configure Consent Screen.
2. Select the User Type that’s allowed for your application.
3. On the first step OAuth consent screen, the basic information you need to input is the App name, User support email and Developer contact information.
4. After filling out the fields, click on Save and continue.
5. On the second (Scopes) and third (Test users) steps, click on Save and continue. On the fourth step (Summary), click on Back to dashboard.

Creating credentials
1. Go back to the credentials tab and now click on + Create Credentials and OAuth Client ID. This time, you'll be able to choose an Application type and create your app.
2. After that, you’ll be redirected to the Credentials screen with a popup showing the information you need: Client ID and Client secret. Those should be noted down so you can later use them in the plugin configuration. 

### Then install the plugin on your site

1. Download the latest release from https://github.com/a8cteam51/google-photos-sync-plugin/releases.
1. Upload and activate the plugin through the `Plugins` menu in WordPress.
2. Once activated, you'll need to configure the App settings which consist of a Client ID and Client Secret obtained from Google when creating a new app. The settings screen for this lives on /wp-admin/admin.php?page=google-photos-sync-settings.
4. Authenticate with Google to grab the credentials (access and refresh token) so you can use them to send requests on your behalf. The settings screen for this is on /wp-admin/admin.php?page=google-photos-sync-authenticate.
5. Create a new Google Photos album to sync to.

### After activation and configuration

The plugin adds a simple block named **Google Photos Album** which will display the selected album to sync images. It’s currently very simple in its layout. The block has an Upload button in the toolbar to allow the user to select images.

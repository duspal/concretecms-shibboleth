# Simple Shibboleth Authentication for ConcreteCMS
A package for simple Shibboleth authentication for ConcreteCMS.  Provides Shibboleth user authentication when mod_shib is installed.

# Installing the Package on Your Concrete CMS

1. Download and extract the ZIP file from Github.
2. Upload or copy the shibboleth directory to your Concrete installations under packages/ (located in the installation root).
3. Activate the plugin through the ConcreteCMS back end (Extend Concrete5 -> Add Functionality, Press Install next to the Simple Shibboleth Authentication item).

# Configuring the Package

1. In the backend of ConcreteCMS, go to Systems & Settings -> Login & Registration -> Authentication Types
2. In the main window, use the arrow keys to move shibboleth to the position you want it to appear on your login screen (the Shibboleth form assumes you're placing "shibboleth/Shibboleth" above "concrete/Standard", though if you pick a different order, see below on how to adjust the login screen text).
3. Again in the main window, click on the shibboleth bar.
4. Click on the "Enable authentication type" to enable Shibboleth Authentication
5. In the newly appearing content below, add your Authentication Url.  This is the url to which Concrete5 will redirect you to activate your Shibboleth session if a Shibboleth session is not activated.
6. (Optional) Add the return URL address in the "Return Url" box if the address is different from the Authentication URL above.  Leave blank if its the same address.
7. Set the log level for teh ConcreteCMS-Shibboleth plugin.  Default is Warning.
8. Click the "Save" button at the bottom right.  If successful, you'll be returned to the previous screen with the message "The Shibboleth authentication type has been enabled." in a green text box.

# Adjusting Login screen text
To add, remove or adjust login screen text, go to /shibboleth/authentication/shibboleth and edit the form.php file.  In here you can change the text as needed for your particular site.

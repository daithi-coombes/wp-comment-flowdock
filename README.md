wp-comment-flowdock
===================

Automatically have wordpress send posts and comments sent to flowdock

The post will be emailed to the flows inbox using the email address of the
current logged in wordpress user.
Any comments on the post will then be updated in the flow inbox as comments on
the submitted post message using the current user's email address, or one 
provided in the comment area, depending on the wordpress theme you are using

This plugin depends on the wordpress [API Connection Manager](https://github.com/david-coombes/api-connection-manager)
and the [flowdock module](https://github.com/david-coombes/api-con-mngr-modules)

Issues
======
Currently there is no method to post comments to the flowdock inbox/influx. This
issue is being looked at by the flowdock team and until further notice this
plugin is not working.

There is also no method with the flowdock api to get organisation/flow names
using the api key so make sure you spell the organisation/flow name correctly.

Usage
=====
When creating or updating a plugin, tick the checkbox beside the flowdock
metabox and click 'publish'/'update' as normal. The metabox may be underneath 
the post editor or two the right depending on your blog setup.

Installation
============
Upload the files to the wp-content/plugins folder on your wordpress blog and
activate in the dashboard (dashboard->plugins)

Navigate to:
Dashboard->Posts->Flowdock
Enter in the API Key for a flow and the flow name in the format:
organisation/flowname


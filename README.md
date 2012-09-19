schedule-from-jira
==================

It's a generator of schedule from Atlassian Jira using API


Generation order
==================

1. Change your config.php. You could write down your user and password for access to jira, url to your jira, list of users and fields for schedule. In addition you could change a list of params for output

2. In index.php you could change an output mode. Now in our system we have 2 modes: html and wikimarkup. 

3. Start index.php in your browser.

4. If you choose "html" mode you can see just a table in your browser. In case of "wikimarkup" mode you need to copy result from your browser. In your Confluence Editor you need to select "Insert -> Wiki Markup", paste it to opened window, press "Insert" and enjoy it!

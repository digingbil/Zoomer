# Zoomer
Simple Zoom JWT API Class for bulk registering users

Ho to use it:
-------------

1. Prerequisite: You should have a paying account in Zoom to be able to require users to register to your meeting.

2. Check the code. You have to add your JWT Key at the top (constant ZOOM_JWTK_KEY), as well as modify the method getUsers(). This should return an array with your users data. Also, you have to add the meeting ID in the constant: ZOOM_MEETING_ID.

3.  Place the Zoomer.php file in your your site's root folder. For example
    
    /user/public\_html/my\_site/Zoomer.php
    
4.  Run it through the browser like
    
    https://mysite.com/Zoomer.php
    
5. Your users should be now registered on your meeting. Depenging on your site the code will decide which approach it will use. Is the site is WordPress or Joomla! then it will use their native API functions.

6. Enjoy.
FSACARS Configuration Guide

ABOUT FSACARS
FSACARS is a Windows-based program to help automate flight reporting for users with Microsoft Flight Simulator or X-Plane. It was developed specifically to be free, easy to use, and fully functional.
Quickly submit accurate flight reports with comprehensive data, including landing rate, block and time aloft, fuel used, crash/slew/pause detection, among others.
First developed more than 20 years ago, FSACARS continues to log thousands of flights every year. Our core model continues to evolve, and new versions have been developed to support different needs, including helicopters, vision-impaired pilots, and custom builds.

https://fsacars.com

================================================================================


INTRODUCTION / QUICKSTART
=========================

A summary of the steps to configuring acars for your VA:

Upload all the server files to one folder on your server.
Edit fsACARS's org.cfg to update the URLs. (see Server Configuration, below)
Distribute fsACARS (with your new serverconfig.txt) to your users.


SERVER CONFIGURATION
====================

1. Modify the following server files, as necessary. Note that all of the server files will work as they are, however you will likely want to customize things for your own server:

  pirep.php - generic flight report receiver
  pirep_mysql.php - alternative flight report receiver using mySQL
  posrep.php - generic position report receiver
  userquery.php - generic user validation
  dispatch.php - generic dispatch
  airports.csv - a list of airports with associated coordinates (used by pirep.php)
  posrep.csv - pilot position reports (created and updated by posrep.php)
  serverlogbook.php - pilot reports are logged here (created and updated by pirep.php)

2. Upload all the server files to your server.
     
3. Edit the org.cfg file located in the folder with fsACARS

orgname - the name of your virtual airline
orgurl - the URL of your website
orglogo - the name of the logo file (set to none if you do not have a license)
orglicense - license key (set to none if you do not have a license)
hashalgo - can be one of: MD5, SHA1, SHA256, SHA512, BCrypt

userquery - path to the userquery.php file
pirep - path to the pirep.php or pirep_mysql.php file
posrep - path to posrep.php
dispatch - path to dispatch.php
serverpass - required (can be anything you like)


4. Distribute fsACARS (with the modified org.cfg) to your users.  Note that they will not need any of the server files or the server folder.



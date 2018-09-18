# Version History :
```
V6 By sebcbien, 18/10/2017
V6.1 by Jojo (19/10/2017)		: server IP adress generated automatically
V6.2 by sebcbien			: added ptz placeholder
V6.3 by Jojo (19/10/2017)		: remove hardcoding of file name & location
V6.4 by Jojo (20/10/2017)		: links are opened in a new tab
V7 by Sebcbien (21/10/2017)		: Added enable/disable action
                              		  Changed file Name (SSS_Get.php)
V8 by Jojo (25/10/2017) 		: add start/stop recording action
					  & global review for alignments, comments, ...
V9 by Jojo (20/11/2017) 		: takes screenshots and send them per e-mail as attachement
					  & actions for all cameras
					  & update menu
V10 by sebcbien (22/11/2017):	Added PTZ function, small bug fixes & rearrange code for speed optimisation
V10.1 by sebcbien (25/11/2017):	correction bug actions.
v10.2 by Jojo (25/11/2017) :    correction bug list PTZ ids & code optimization (use function)
v11 by Jojo (23/12/2017) :	get configuration from external file
v11.1 by Jojo (22/06/2018) :	add TimeStamps for display in logs
v12 by Jojo (14/09/2018) :	add possibility to personnalize subject of the e-amil
v13 by seb (16/09/2018) :	add elapsed time counter for debug purposes
				solved bug ini file not parsed when YAPUSS script is not in the root folder
ToDo:
 - accept array of cameras form url arguments
```
# Installation instructions :
```php
Install php 7.0 on the Web server.
Save this file with extension .php (example : SSS_Get.php)
In the same folder, create the .ini file with the SAME name (except the extension) as this scirpt file (example : SSS_Get.ini)

If you use Synology to send mails. you need to configure the notification in the control panel.
I share with you some strange behaviors.
	1) When using GMAIL, even if the test mail notification was ok, this php mail was not send. => Solution is to re-do the autentication for Gmail in the notification Panel of the Synology. But after few hours / days it does not work anymore ...
	2) So I tried Yahoo! as SMTP provider, but the delivery of mails tooks a long time (several seconds/minutes)
	3) I use now the SMTP of my mail provider : this is as fast as whith Gmail.

```
# Thread here :
```php
https://www.domotique-fibaro.fr/topic/11097-yapuss-passerelle-universelle-surveillance-station/
Thanks to all open sources examples grabbed all along the web and specially filliboy who made this script possible.

```
# Some Examples :
```php
- Main functions: Get Snapshot:
http://xxxxxx/SSS_Get.php?stream_type=jpeg&camera=19&stream=1  - returns a snapshot of camera Nr 19, stream Nr 1
	Stream arguments: 0->Live stream | 1->Recording stream | 2->Mobile stream   (default value: 0)
- Main functions: Get Mjpeg Stream:
http://xxxxxx/SSS_Get.php?stream_type=mjpeg&camera=19          - returns a mjpeg stream of camera 19

Help function:
http://xxxxxx/SSS_Get.php                                      - Returns the list of all cameras with a snapshot, status, urls etc.
http://xxxxxx/SSS_Get.php?list=json                            - Returns a json with all cameras
http://xxxxxx/SSS_Get.php?list=camera                          - Returns the list of all cameras with a snapshot, status, urls etc.

Other functions:
- Enable/Disable Camera
http://xxxxxx/SSS_Get.php?action=enable&camera=14              - enable camera 14
http://xxxxxx/SSS_Get.php?action=enable&camera=0               - enable ALL cameras
http://xxxxxx/SSS_Get.php?action=enable                        - enable ALL cameras
http://xxxxxx/SSS_Get.php?action=disable&camera=12             - disable camera 12
http://xxxxxx/SSS_Get.php?action=disable&camera=0              - disable ALL cameras
http://xxxxxx/SSS_Get.php?action=disable                       - disable ALL cameras
- Start/Stop recording
http://xxxxxx/SSS_Get.php?action=start&camera=14               - start recording camera 14 (the camera is enabeled if disabeled)
http://xxxxxx/SSS_Get.php?action=start&camera=0                - start recording ALL cameras (the desabled cameras are enabeled)
http://xxxxxx/SSS_Get.php?action=start                         - start recording ALL cameras (the desabled cameras are enabeled)
http://xxxxxx/SSS_Get.php?action=stop&camera=14                - stop recording camera 14
http://xxxxxx/SSS_Get.php?action=stop&camera=0                 - stop recording ALL cameras
http://xxxxxx/SSS_Get.php?action=stop                          - stop recording ALL cameras
- Send Scrennshot by Email
http://xxxxxx/SSS_Get.php?action=mail&camera=14                - send per mail screenshot of camera 14
http://xxxxxx/SSS_Get.php?action=mail&camera=0                 - send per mail screenshot of ALL cameras
http://xxxxxx/SSS_Get.php?action=mail                          - send per mail screenshot of ALL cameras
http://xxxxxx/SSS_Get.php?action=mail&subject=non default      - send per mail screenshot of ALL cameras with the non default subject on the mail
- PTZ function
http://xxxxxx/SSS_Get.php?ptz=5&camera=19                      - moves camera to PTZ position id 5
for action=start & action=mail, adding the parameter '&enable=1' enable the disabled camera before the action.
```

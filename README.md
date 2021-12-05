# camAcquire
# Raspberry Pi Camera capture and pre and post processing scripts

## System setup

The capture script needs permissions to access the video subsystem. This can be accomplished by running raspistill with sudo or by adding a udev rule to
allow members of the video group to access the video subsystem. 
```
echo 'SUBSYSTEM=="vchiq",GROUP="video",MODE="0660"' >> /etc/udev/rules.d/10-vchiq-permissions.rules
```

And then adding the user ("aprs" in example below) to the video group.
```
usermod -a -G video aprs
```

Note that if running interactively, you will need to logout and log back in before the user is actually added to the video group.

The camera, by default, will have a red LED lit when the camera is active. This can reflect back into the c
camera and cause glare. Disable in `/boot/config.txt` with `disable_camera_led=1`

## Requirements

PHP (CLI version) and ImageMagick are required. Install with:
`sudo apt-get install imagemagick php-cli`

We use short open tags which must be set in `php.ini`.

## actions.pre/ and actions.post/ directories

Contain scripts that are run before and after acquiring camera image. The capture script runs the executeable scripts in sorted order. Each scrip it called with the following arguments:

```
argv description
[0]  name of script (standard unix practice)
[1]  unix timestamp of when camera image acquired
[2]  filename (with path) of full size image
[3]  filename (with path) of thumbnail size image
```

### actions.pre/ contains scripts to be run prior to acquiring camera image

examples would be acquiring sensor data, turning on illumination, etc. 

Note that arguments 2 and 3 (full size and thumbnail image filename) probably will be old or not exist yet. 

Also note that the execution time of the actions.pre scripts will delay the start time of the camera image acquisition. This can be nearly eliminated by having the script go into the background and immediately return.

### actions.post/ contains scripts to be run post acquiring camera image

examples would be watermarking image, uploading image, adding EXIF data,
storing on SD card, etc.


## Web interface
A `monkey` web server can be installed with:

```
apt-get install php-cgi monkey
cp /etc/monkey/monkey.conf /etc/monkey/monkey.conf.origin
cp www/monkey/monkey.conf /etc/monkey/monkey.conf
```
In `/etc/php/7.3/cgi/php.ini` we need `cgi.force_redirect = 0`

Monkey will only run on non-root ports. So we have it running on port 8080 and we use an iptables rule to make it also appear on port 80. Set that up with
```
iptables -t nat -A PREROUTING -p tcp --dport 80 -j REDIRECT --to-port 8080
apt-get install iptables-persistent
iptables-save > /etc/iptables/rules.v4
```

And basic functionality can be installed with:
```
rm -rf /var/www/*
cp www/html/index.php /var/www
ln -s /run/shm/cam /var/www/cam


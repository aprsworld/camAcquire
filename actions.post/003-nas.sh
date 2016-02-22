#!/bin/bash
# Store on samba drive
cd /run/shm/cam/latest
smbclient '\\192.168.30.4\photo' stainless -D 'webcam/turkey' -U aprs -c 'put latest.jpg'


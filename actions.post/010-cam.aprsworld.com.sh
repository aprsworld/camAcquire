#!/bin/bash

# replace usname:password with your cam.aprsworld.com username and password

curl -F file=@$3 http://A5574:pond@cam.aprsworld.com/up/
retVal=$?

if [ $retVal -eq 0 ]; then
	# reset write watchdog if curl returned 0
	echo "# resetting write watchdog since curl exited error free"
	/home/aprs/aprsI2C/aprs/pzPowerI2C/pzPowerI2C --reset-write-watchdog
else
	echo "# curl returned $retVal which is an error. Not resetting write watchdog"
fi

exit $retVal




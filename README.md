# camAcquire
# Raspberry Pi Camera capture and pre and post processing scripts

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

#!/usr/bin/env python

import os
import sys
import subprocess
import shutil
import time
import datetime

basedir = "/var/www/streamer.ecolitan.net.nz"
queuedir = os.path.join(basedir, "queue")
processingdir = os.path.join(basedir, "processing")
videodir = os.path.join(basedir, "video")
logdir = os.path.join(basedir, "log")

def log(job, msg):
    ts = time.time()
    st = datetime.datetime.fromtimestamp(ts).strftime('%Y-%m-%d %H:%M:%S')
    logfile = os.path.join(logdir, job)
    with open(logfile, "a") as f:
        f.write(st + " " + msg + "\n")

# Get a job to process
queue = os.listdir(queuedir)
try:
    job = queue[0]
except IndexError:
    sys.exit(0)

logfile = os.path.join(logdir, job)

# Move job file into in-progress state
log(job,"starting {}".format(job))
shutil.move(os.path.join(queuedir, job), os.path.join(processingdir, job))

# Determine Video filename
filename = job[:-4]

# Determine full input video path
with open(os.path.join(processingdir, job)) as f:
    input_vidpath = os.path.join(f.read(),  filename)

# Determine output video path
output_vidpath = os.path.join(videodir, filename + ".mp4")

# Encode file and write output to logfile
encode_command = ['/usr/bin/avconv', '-i', input_vidpath, '-y', '-c:v', 'libx264', '-strict', 'experimental', output_vidpath]
log(job, str(encode_command))
encodeprocess = subprocess.Popen(encode_command, stderr=open(logfile, "a"), stdout=open(logfile, "a"))
returncode = encodeprocess.wait()
os.remove(os.path.join(processingdir, job))

if returncode != 0:
    log(job, "an error occured processing the video")
    sys.exit(1)

# Write html file
html_filepath = os.path.join(videodir, filename + ".html")
openhtml = """<html>
<head>
  <title>{}</title>
</head>
<body>
  <video id="{}" src="{}" controls>mp4</video>
  <br><br>
</body>
</html>
""".format(filename, filename, filename + ".mp4")

with open(html_filepath, "w") as f:
    f.write(openhtml)

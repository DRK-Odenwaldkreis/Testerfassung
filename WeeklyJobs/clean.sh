#!/bin/bash

# This file is part of DRK Testerfassung.

echo "Starting Cleaning"
cd /home/webservice/Testerfassung/NightlyAutoClean && python3 job.py >> ../../Logs/cleanJob.log 2>&1
python3 job.py
echo "Cleaning complete"
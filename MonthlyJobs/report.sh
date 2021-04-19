#!/bin/bash

# This file is part of DRK Testerfassung.

echo "Starting Month Report"
cd /home/webservice/Testerfassung/MonatsReport
python3 job.py $(date '+%m' -d "-1 month") $(date '+%Y') 1
echo "Month Report Done"
#!/bin/bash

# This file is part of DRK Testerfassung.

echo "Starting Accounting"
cd /home/webservice/Testerfassung/AccountJob
python3 job.py $(date '+%Y-%m-%d')
echo "Accounting complete"
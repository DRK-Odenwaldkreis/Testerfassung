#!/bin/bash

# This file is part of DRK Testerfassung.

echo "Starting Reminding"
cd /home/webservice/Testerfassung/AppointmentReminderJob
python3 job.py $(date '+%Y-%m-%d')
echo "Reminding complete"
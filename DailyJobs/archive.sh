#!/bin/bash

# This file is part of DRK Testerfassung.

echo "Starting Archive"
cd /home/webservice/Testerfassung/ArchiveJob
python3 job.py $(date '+%Y-%m-%d' -d "1 day ago")
echo "Archive complete"
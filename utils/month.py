import sys

# This file is part of DRK Testerfassung

def monthInt_to_string(month):
    try:
        monate = ("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember")
        return monate[int(month)-1]
    except:
        return "Unknown"

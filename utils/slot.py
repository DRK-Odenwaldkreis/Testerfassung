#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testerfassung.


import os
import sys

def get_slot_time(slot, stunde):
    if slot == 1:
        start = '00'
        ende = '15'
        stundeStart = stunde
        stundeEnd = stunde
    elif slot == 2:
        start = '15'
        ende = '30'
        stundeStart = stunde
        stundeEnd = stunde
    elif slot == 3:
        start = '30'
        ende = '45'
        stundeStart = stunde
        stundeEnd = stunde
    elif slot == 4:
        start = '45'
        ende = '00'
        stundeStart = stunde
        stundeEnd = stunde + 1
    else:
        start ='00'
        ende = '00'
        stundeStart = stunde
        stundeEnd = stunde
    appointment = "%s:%s - %s:%s" % (str(stundeStart),str(start),str(stundeEnd),str(ende))
    return appointment

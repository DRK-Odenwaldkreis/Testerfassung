#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testerfassung.


import os
import sys

def get_slot_time(slot, stunde):
    start ='00'
    ende = '00'
    if slot == 1:
        start = '00'
        ende = '15'
    elif slot == 2:
        start = '15'
        ende = '30'
    elif slot == 3:
        start = '30'
        ende = '45'
    elif slot == 4:
        start = '45'
        ende = '00'
    if slot !=4:
        stundeStart = stunde
        stundeEnd = stunde
    else:
        stundeStart = stunde
        stundeEnd = stunde + 1
    appointment = "%s:%s - %s:%s" % (str(stundeStart),str(start),str(stundeEnd),str(ende))
    return appointment

def start_time(slot, stunde):
    start ='00'
    ende = '00'
    if slot == 1:
        start = '00'
        ende = '15'
    elif slot == 2:
        start = '15'
        ende = '30'
    elif slot == 3:
        start = '30'
        ende = '45'
    elif slot == 4:
        start = '45'
        ende = '00'
    if slot !=4:
        stundeStart = stunde
        stundeEnd = stunde
    else:
        stundeStart = stunde
        stundeEnd = stunde + 1
    start = "%s:%s:00" % (str(stundeStart),str(start))
    return start

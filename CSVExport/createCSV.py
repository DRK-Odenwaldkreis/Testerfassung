#!/usr/bin/env python
# -*- coding: utf-8 -*-
# This file is part of DRK Testerfassung.


import sys
import csv
import logging

from datetime import datetime
sys.path.append("..")


logger = logging.getLogger('CSV Export')
logger.debug('Logger for createCSV was initialised')


def create_CSV(content, date):
    filename = "../../Reports/export_" + str(date) + ".csv"
    with open(filename, mode='w', newline='') as csvfile:
        writeEntry = csv.writer(csvfile, delimiter=';')
        writeEntry.writerow(["id",
                             "Nachname",
                             "Vorname",
                             "Geburtsdatum",
                             "Wohnort",
                             "Adresse,",
                             "Telefonnummer",
                             "Mailadresse",
                             "Ergebnis",
                             "Testzeitpunkt",
                             "Teststation",
                             "Test-Typ"
                             ])
        for i in content:
            writeEntry.writerow(i)
    return filename

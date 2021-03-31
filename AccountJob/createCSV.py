#!/usr/bin/env python
# -*- coding: utf-8 -*-
# This file is part of DRK Testerfassung.


import sys
import csv
import logging

from datetime import datetime
sys.path.append("..")
from utils.database import Database


logger = logging.getLogger('CSV Export Accounting')
logger.debug('Logger for createCSV was initialised')


def create_CSV(content, month, year):
    filename = "../../Reports/export_abrechnung_" + str(month) + "_" + str(year) + ".csv"
    with open(filename, mode='w', newline='') as csvfile:
        writeEntry = csv.writer(csvfile, delimiter=';')
        writeEntry.writerow(["Station",
                             "Name",
                             "Datum",
                             "Anzahl"
                             ])
        for i in content:
            writeEntry.writerow(i)
    return filename

logFile = '../../Logs/AccountingExport.log'
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('CSV Export')
logger.debug('Starting')


if __name__ == "__main__":
    try:
        
        if len(sys.argv) == 3:
            requestedMonth = sys.argv[1]
            requestedYear = sys.argv[2]
            sql = "Select Teststation,Station.Ort,Date,Amount from Abrechnung JOIN Station on Abrechnung.Teststation=Station.id where MONTH(Date)=%s and YEAR(Date)=%s order by Teststation;" % (requestedMonth,requestedYear)
        else:
            logger.debug(
                'Input parameters are not correct, month and year needed')
            raise Exception
        logger.debug('Getting all Events for employee of the month and year with the following query: %s' % (sql))
        DatabaseConnect = Database()
        exportEvents = DatabaseConnect.read_all(sql)
        logger.debug('Received the following entries: %s' %(str(exportEvents)))
        filename = create_CSV(exportEvents, requestedMonth, requestedYear) 
        print(filename.replace('../../Reports/', ''))
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

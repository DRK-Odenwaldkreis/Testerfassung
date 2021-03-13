#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testerfassung.


import logging
import locale
import time
import datetime
import sys
sys.path.append("..")
from utils.database import Database
from createCSV import create_CSV
from utils.sendmail import send_csv_report

logFile = '../../Logs/CSVExportJob.log'
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('CSV Export')
logger.debug('Starting')


if __name__ == "__main__":
    try:
        if len(sys.argv) == 2:
            requestedDate = sys.argv[1]
            gesundheitsamt=False
            sql = "Select id,Nachname,Vorname,Geburtsdatum,Adresse,Telefon,Mailadresse,Ergebnis,Ergebniszeitpunkt,Teststation from Vorgang where and Ergebniszeitpunkt Between '%s 00:00:00' and '%s 23:59:59';" % (
            requestedDate.replace('-', '.'), requestedDate.replace('-', '.'))
        elif len(sys.argv) == 3:
            requestedDate = sys.argv[1]
            gesundheitsamt=True
            sql = "Select id,Nachname,Vorname,Geburtsdatum,Adresse,Telefon,Mailadresse,Ergebnis,Ergebniszeitpunkt,Teststation from Vorgang where Ergebnis = 1 and Ergebniszeitpunkt Between '%s 00:00:00' and '%s 23:59:59';" % (
                requestedDate.replace('-', '.'), requestedDate.replace('-', '.'))
        else:
            logger.debug(
                'Input parameters are not correct, date and/or gesundheitsamt needed')
            raise Exception
        DatabaseConnect = Database()
        logger.debug(
            'Getting all Events for employee of the month and year with the following query: %s' % (sql))
        exportEvents = DatabaseConnect.read_all(sql)
        logger.debug('Received the following entries: %s' %
                     (str(exportEvents)))
        if gesundheitsamt:
            filename = create_CSV(exportEvents, requestedDate) + '_gesundheitsamt_'
        else:
            filename = create_CSV(exportEvents, requestedDate)
        logger.debug('Done')
        print(filename.replace('../../Reports/', ''))
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

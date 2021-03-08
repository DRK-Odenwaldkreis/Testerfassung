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

logFile = '../../Logs/CSVExportJob.log'
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('CSV Export')
logger.debug('Starting')


if __name__ == "__main__":
    try:
        if len(sys.argv) != 2:
            logger.debug(
                'Input parameters are not correct, date is needed')
            raise Exception
        logger.debug(
            'Was started for the following day: %s' % (sys.argv[1]))
        requestedDate = sys.argv[1]
        DatabaseConnect = Database()
        sql = "Select id,Nachname,Vorname,Geburtsdatum,Adresse,Telefon,Mailadresse,Ergebnis,Ergebniszeitpunkt,Teststation from Vorgang where DATE(Ergebniszeitpunkt)='%s';" % (requestedDate)
        logger.debug('Getting all Events for employee of the month and year with the following query: %s' % (sql))
        exportEvents = DatabaseConnect.read_all(sql)
        logger.debug('Received the following entries: %s' %
                     (str(exportEvents)))
        filename = create_CSV(exportEvents, requestedDate)
        logger.debug('Done')
        print(filename.replace('../../Reports/', ''))
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

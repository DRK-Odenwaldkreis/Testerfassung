#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
import datetime

logFile = '../../Logs/accountJob.log'
logging.basicConfig(filename=logFile, level=logging.DEBUG,format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Accounting job stated: %s'%(datetime.datetime.now())
logger.debug('Starting')

if __name__ == "__main__":
    try:
        if len(sys.argv) == 2:
            requestedDate = sys.argv[1]
        else:
            logger.debug(
                'Input parameters are not correct, date and/or requested needed')
            raise Exception
        DatabaseConnect = Database()
        sql = "Select Count(id), Teststation from Vorgang where Ergebniszeitpunkt Between '%s 00:00:00' and '%s 23:59:59' GROUP BY Teststation;" % (requestedDate.replace('-', '.'), requestedDate.replace('-', '.'))
        logger.debug('Getting all Events for a date with the following query: %s' % (sql))
        statistics = DatabaseConnect.read_all(sql)
        for station in statistics:
            sql = "INSERT INTO Abrechnung (Teststation,Date,Amount) VALUES(%s,%s,%s);" % (station[1],requestedDate,station[0])
            DatabaseConnect.insert(sql)
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

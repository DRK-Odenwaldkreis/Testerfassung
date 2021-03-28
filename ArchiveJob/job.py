#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
import datetime

logFile = '../../Logs/archieveJob.log'
logging.basicConfig(level=logging.DEBUG,format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Archieve job stated: %s'%(datetime.datetime.now()))
logger.debug('Starting')

if __name__ == "__main__":
    try:
        if len(sys.argv) == 2:
            requestedDate = datetime.datetime.strptime(sys.argv[1], '%Y-%m-%d').date()
        else:
            logger.debug(
                'Input parameters are not correct, date needed')
            raise Exception
        DatabaseConnect = Database()
        sql = "Select id, Teststation, Token, Registrierungszeitpunkt from Vorgang where (Ergebnis != 1 and DATE(Registrierungszeitpunkt) <= '%s') or (Ergebnis = 1 and Date(Registrierungszeitpunkt) <= '%s');" % (requestedDate-datetime.timedelta(days=2),requestedDate-datetime.timedelta(days=90))
        logger.debug('Getting all Events for a date with the following query: %s' % (sql))
        deleteCanidate = DatabaseConnect.read_all(sql)
        for i in deleteCanidate:
            sql = "INSERT INTO Archive (TestNr, Station, Token, Registrierungszeitpunkt) VALUES (%s,%s,%s,%s);"
            tupel = (i)
            if DatabaseConnect.insert(sql,tupel):
                sql = "Delete Vorgang where id=%s"%(i[0])
                DatabaseConnect.delete(sql)
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

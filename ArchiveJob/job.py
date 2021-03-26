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
            requestedDate = sys.argv[1]
        else:
            logger.debug(
                'Input parameters are not correct, date needed')
            raise Exception
        DatabaseConnect = Database()
        sql = "Select id, Teststation, Token, Registrierungszeitpunkt from Vorgang where Ergebnis != 1 and Registrierungszeitpunkt Between '%s 00:00:00' and '%s 23:59:59';" % (requestedDate.replace('-', '.'), requestedDate.replace('-', '.'))
        logger.debug('Getting all Events for a date with the following query: %s' % (sql))
        deleteCanidate = DatabaseConnect.read_all(sql)
        for i in deleteCanidate:
            print(i)
            sql = ""
            print(sql)
            tupel = (i)
            print(tupel)
            if DatabaseConnect.insert(sql,tupel):
                sql = "Delete Vorgang where id=%s"%(i[0])
                print(sql)
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

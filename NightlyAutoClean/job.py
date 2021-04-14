#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
import datetime

logFile = '../../Logs/cleanJob.log'
logging.basicConfig(filename=logFile, level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Nightly Auto Clean started on: %s'%datetime.datetime.now())
logger.info('Starting nightly clean')

if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        sql = "Delete from Kartennummern where Used=1;"
        logger.debug('Cleaning all Kartennummern that were used, using the following query: %s' % (sql))
        DatabaseConnect.delete(sql)
        sql = "Delete from Voranmeldung where Used = 1;"
        logger.debug('Cleaning all Voranmeldungen that were used, using the following query: %s' % (sql))
        DatabaseConnect.delete(sql)
        sql = "Delete from Voranmeldung where Tag <= (NOW() - INTERVAL 1 DAY);"
        logger.debug('Cleaning all Voranmeldungen that are prior today, using the following query: %s' % (sql))
        DatabaseConnect.delete(sql)
        sql = "Delete from Termine where Tag <= (NOW() - INTERVAL 2 DAY);"
        logger.debug('Cleaning all Termine that are prior today, using the following query: %s' % (sql))
        DatabaseConnect.delete(sql)
        logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

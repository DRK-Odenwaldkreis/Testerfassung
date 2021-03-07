#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
from utils.sendmail import send_negative_result
from utils.sendmail import send_positive_result
from utils.sendmail import send_indistinct_result
import datetime

logFile = '../../Logs/rotationJob.log'
logging.basicConfig(filename=logFile, level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Rendition Job startet on: %s') %(datetime.datetime.now())
logger.debug('Starting')

if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        sql = "Select Vorname,Nachname,Mailadresse,Ergebnis,id from Vorgang where Mailsend = 0;"
        logger.debug('Checking for new results, using the following query: %s' % (sql))
        content = DatabaseConnect.read_all(sql)
        for i in content:
            result = i[3]
            nachname = i[1]
            vorname = i[0]
            mail = i[2]
            if result == 0:
                transmission = send_negative_result(vorname,nachname,mail)
            elif result == 1:
                transmission = send_positive_result(vorname, nachname, mail)
            else:
                transmission = send_indistinct_result(vorname, nachname, mail)
            logger.debug('Checking if entry for mailsend can be set to true')
            if transmission:
                sql = "Update id for id"
                DatabaseConnect.update(sql)
        logger.debug('Done')
    except Exception as e:
        logging.error("Error")

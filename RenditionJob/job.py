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
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Rendition Job startet on: %s' %(datetime.datetime.now()))
logger.debug('Starting')

if __name__ == "__main__":
    try:
        if len(sys.argv) != 2:
            logger.debug('Input parameters are not correct, station id is needed')
            raise Exception
        stationID = sys.argv[1]
        DatabaseConnect = Database()
        sql = "Select Vorname,Nachname,Mailadresse,Ergebnis,Registrierungszeitpunkt, id from Vorgang where Mailsend is NULL and Mailadresse is not NULL and Teststation = %s;" % (
            stationID)
        logger.debug('Checking for new results, using the following query: %s' % (sql))
        content = DatabaseConnect.read_all(sql)
        for i in content:
            result = i[3]
            nachname = i[1]
            vorname = i[0]
            mail = i[2]
            date = i[4].strftime("%A, den %d.%m.%Y")
            testID = i[5]
            if result == 0:
                transmission = send_negative_result(
                    vorname, nachname, mail, date)
            elif result == 1:
                transmission = send_positive_result(
                    vorname, nachname, mail, date)
            else:
                transmission = send_indistinct_result(
                    vorname, nachname, mail, date)
            logger.debug('Checking if entry for mailsend can be set to true')
            if transmission:
                sql = "Update Vorgang SET Mailsend = 1 WHERE id = %s;" % (testID)
                DatabaseConnect.update(sql)
        logger.debug('Done')
    except Exception as e:
        logging.error("Error")

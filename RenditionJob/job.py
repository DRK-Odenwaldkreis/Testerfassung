#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

import datetime
import locale
from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
from utils.sendmail import send_negative_result
from utils.sendmail import send_positive_result
from utils.sendmail import send_indistinct_result
from utils.sendmail import send_new_entry

locale.setlocale(locale.LC_ALL, 'de_DE')

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
        sql = "Select Vorname,Nachname,Mailadresse,Ergebnis,Registrierungszeitpunkt, id, Adresse, Telefon, Geburtsdatum  from Vorgang where Mailsend is NULL and Mailadresse is not NULL and Teststation = %s;" % (
            stationID)
        logger.debug('Checking for new results, using the following query: %s' % (sql))
        content = DatabaseConnect.read_all(sql)
        if len(content) > 0:
            for i in content:
                result = i[3]
                nachname = i[1]
                vorname = i[0]
                mail = i[2]
                date = i[4].strftime("%A, den %d.%m.%Y um %H%M")
                testID = i[5]
                adresse = i[6]
                telefon = i[7]
                geburtsdatum = i[8]
                if result == 2:
                    transmission = send_negative_result(vorname, nachname, mail, date)
                elif result == 1:
                    transmission = send_positive_result(vorname, nachname, mail, date)
                    transmission_gesundheitsamt = send_new_entry(date)
                else:
                    transmission = send_indistinct_result(vorname, nachname, mail, date)
                logger.debug('Checking if entry for mailsend can be set to true')
                if transmission and transmission_gesundheitsamt:
                    sql = "Update Vorgang SET Mailsend = 1 WHERE id = %s;" % (testID)
                    DatabaseConnect.update(sql)
        else:
            logger.debug('Nothing to do')
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

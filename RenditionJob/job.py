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
from utils.sendmail import send_notification

#locale.setlocale(locale.LC_ALL, 'de_DE')

logFile = '../../Logs/rotationJob.log'
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Rendition Job startet on: %s' %(datetime.datetime.now()))
logger.debug('Starting')

if __name__ == "__main__":
    try:
        if len(sys.argv) == 2:
            logger.debug('Input parameters are not correct, station id is needed')
            stationID = sys.argv[1]
            sql = "Select Vorname,Nachname,Mailadresse,Ergebnis,Registrierungszeitpunkt, id, Adresse, Telefon, Geburtsdatum  from Vorgang where Mailsend is NULL and Ergebnis is not NULL and Teststation = %s;" % (
            stationID)
        else:
            sql = "Select Vorname,Nachname,Mailadresse,Ergebnis,Registrierungszeitpunkt, id, Adresse, Telefon, Geburtsdatum  from Vorgang where Mailsend is NULL and Ergebnis is not NULL;"
        DatabaseConnect = Database()
        logger.debug('Checking for new results, using the following query: %s' % (sql))
        content = DatabaseConnect.read_all(sql)
        logger.debug(
            'Received the following content: %s' % (content))
        if len(content) > 0:
            logger.debug(
                'Content contains infos')
            for i in content:
                result = i[3]
                nachname = i[1]
                vorname = i[0]
                mail = i[2]
                date = i[4].strftime("%d.%m.%Y um %H:%M Uhr")
                testID = i[5]
                adresse = i[6]
                telefon = i[7]
                geburtsdatum = i[8]
                transmission = True
                transmission_gesundheitsamt = True
                if len(mail) > 0:
                    logger.debug(
                        'Mailadress seems to be enterd')
                    if result == 2:
                        logger.debug(
                            'Sending negative result Mail')
                        transmission = send_negative_result(vorname, nachname, mail, date, geburtsdatum)
                    elif result == 1:
                        logger.debug(
                            'Sending positive result Mail')
                        transmission = send_positive_result(vorname, nachname, mail, date, geburtsdatum)
                        transmission_gesundheitsamt = send_new_entry(date)
                    elif result == 9:
                        logger.debug(
                            'Sending indistinct result Mail')
                        transmission = send_indistinct_result(vorname, nachname, mail, date, geburtsdatum)
                    else:
                        logger.debug('Sending support mail because can not interpret result')
                        send_notification(vorname,nachname,date)
                    logger.debug('Checking if entry for mailsend can be set to true')
                else:
                    logger.debug(
                        'Mailadress seems to be not enterd')
                    tansmission = True
                    if result == 1:
                        logger.debug(
                            'Sending positive mail to gesundheitsamt only')
                        transmission_gesundheitsamt = send_new_entry(date)
                logger.debug('Checking whether mail was send properly and closing db entry')
                if transmission and transmission_gesundheitsamt:
                    logger.debug('Mail was succesfully send, closing entry in db')
                    sql = "Update Vorgang SET Mailsend = 1 WHERE id = %s;" % (
                        testID)
                    DatabaseConnect.update(sql)
        else:
            logger.debug('Nothing to do')
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

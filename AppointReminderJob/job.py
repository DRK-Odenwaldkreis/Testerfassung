#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
from utils.sendmail import send_mail_reminder
import datetime

logFile = '../../Logs/reminderJob.log'
logging.basicConfig(filename=logFile, level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Reminder job for appointment started on: %s')%(datetime.datetime.now())
logger.debug('Starting')

if __name__ == "__main__":
    try:
        if len(sys.argv) == 2:
            requestedDate = sys.argv[1]
        else:
            logger.debug('Input parameters are not correct, date needed')
            raise Exception
        DatabaseConnect = Database()
        sql = "Select Vorname, Nachname, Mailadresse, Slot, Stunde from Voranmeldung where Tag =%s;" % (requestedDate)
        logger.debug('Getting all appointments for %s, using the following query: %s' % (requestedDate,sql))
        recipients = DatabaseConnect.read_all(sql)
        for i in recipients:
            slot = i[3]
            vorname = i[0]
            nachname = i[1]
            stunde = i[4]
            mail = i[2]
            if slot == 1:
                start = '00'
                ende = '15'
            elif slot == 2:
                start = '15'
                ende = '30'
            elif slot == 3:
                start = '30'
                ende = '45'
            elif slot == 4:
                start = '45'
                ende = '00'
            appointment = "%s:%s - %s:%s" % (str(stunde),str(start),str(stunde),str(ende))
            send_mail_reminder(mail, requestedDate,vorname, nachname, appointment)
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
from utils.sendmail import send_cancel_appointment
import datetime

logFile = '../../Logs/cancelJob.log'
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Cancel job for appointment cancelation started on: %s'%(datetime.datetime.now()))
logger.debug('Starting')

if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        sql = "Select Voranmeldung.Vorname, Voranmeldung.Nachname, Voranmeldung.Mailadresse, Voranmeldung.Tag, Voranmeldung.id from Voranmeldung LEFT JOIN Termine ON Termine.id=Voranmeldung.Termin_id where Termine.Tag is NULL;"
        logger.debug('Cancel all appointments, using the following query: %s' % (sql))
        canceledAppointments = DatabaseConnect.read_all(sql)
        logger.debug('Received the following cancel objects: %s' %(str(canceledAppointments)))
        for i in canceledAppointments:
            logger.debug('Received the following entry: %s' %(str(i)))
            vorname = i[0]
            nachname = i[1]
            mail = i[2]
            entry = i[4]
            date = i[3]
            logger.debug('Handing over to sendmail of reminder')
            if send_cancel_appointment(mail, date, vorname, nachname):
                logger.debug('Mail was succesfully send, closing entry in db')
                sql = "Delete from Voranmeldung where id = %s;" % (entry)
                DatabaseConnect.delete(sql)
        logger.debug('Done for all')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

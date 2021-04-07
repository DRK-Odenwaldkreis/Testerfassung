#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
from utils.sendmail import send_mail_reminder
from utils.slot import get_slot_time
from TicketGeneration.pdfcreator.pdf import PDFgenerator
import datetime

logFile = '../../Logs/reminderJob.log'
logging.basicConfig(filename=logFile,level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Reminder job for appointment started on: %s'%(datetime.datetime.now()))
logger.info('Starting reminder of Appointments')

if __name__ == "__main__":
    try:
        if len(sys.argv) == 2:
            requestedDate = sys.argv[1]
        else:
            logger.debug('Input parameters are not correct, date needed')
            raise Exception
        DatabaseConnect = Database()
        sql = "Select Voranmeldung.Vorname, Voranmeldung.Nachname, Voranmeldung.Mailadresse, Termine.Slot, Termine.Stunde, Voranmeldung.Tag, Voranmeldung.Token, Voranmeldung.id, Station.Ort, Station.Adresse, Termine.opt_station_adresse, Termine.opt_station from Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id JOIN Station ON Termine.id_station=Station.id where Voranmeldung.Tag Between '%s 00:00:00' and '%s 23:59:59' and Reminded = 0 and Termine.Slot is not NULL;" % (requestedDate,requestedDate)
        logger.debug('Getting all appointments for %s, using the following query: %s' % (requestedDate,sql))
        recipients = DatabaseConnect.read_all(sql)
        logger.debug('Received the following recipients: %s' %(str(recipients)))
        for i in recipients:
            try:
                logger.debug('Received the following entry: %s' %(str(i)))
                slot = i[3]
                vorname = i[0]
                nachname = i[1]
                stunde = i[4]
                mail = i[2]
                entry = i[7]
                token = i[6]
                date = i[5]
                ort = i[8]
                adress = i[9]
                opt_ort = i[10]
                opt_adress = i[11]
                appointment = get_slot_time(slot,stunde)
                if len(opt_ort) == 0:
                    location = str(ort) + ", " + str(adress)
                else:
                    location = str(opt_ort) + "," + str(opt_adress)
                PDF = PDFgenerator()
                filename = PDF.creatPDF(i,location)
                logger.debug('Handing over to sendmail of reminder')
                url = "https://testzentrum-odw.de/registration/index.php?cancel=cancel&t=%s&i=%s" % (token, entry)
                if send_mail_reminder(mail, date, vorname, nachname, appointment, url, filename):
                    logger.debug('Mail was succesfully send, closing entry in db')
                    sql = "Update Voranmeldung SET Reminded = 1 WHERE id = %s;" % (entry)
                    DatabaseConnect.update(sql)
            except Exception as e:
                logging.error("The following error occured in loop of recipients: %s" % (e))
        logger.info('Done for all')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

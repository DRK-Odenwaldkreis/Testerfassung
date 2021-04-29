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

logFile = '../../Logs/clean.log'
logging.basicConfig(filename=logFile,level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Cleaning for not verified appointments started on: %s'%(datetime.datetime.now()))
logger.info('Starting cleaner of unverified appointments')

if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        sql = "Select Voranmeldung_Verif.id, Voranmeldung.id, Voranmeldung.Termin_id FROM Voranmeldung_Verif JOIN Voranmeldung ON Voranmeldung_Verif.id_preregistration = Voranmeldung.id  WHERE Voranmeldung_Verif.updated < (NOW() - INTERVAL 15 MINUTE);"
        logger.debug('Finding all unverified appointments using the following query: %s' % (sql))
        unverified = DatabaseConnect.read_all(sql)
        for i in unverified:
            try:
                termine_id = i[2]
                sql = "Update Termine SET Used = NULL where id=%s;"%(termine_id)
                logger.debug('Finding used Termin setting back to NULL using the following query: %s' % (sql))
                DatabaseConnect.update(sql)
                voranmeldung_id = i[1]
                sql = "Delete from Voranmeldung where id=%s;"%(voranmeldung_id)
                logger.debug('Deleting Voranmeldung using the following query: %s' % (sql))
                DatabaseConnect.delete(sql)
                verif_id=i[0]
                sql = "Delete from Voranmeldung_Verif where id=%s;"%(verif_id)
                logger.debug('Deleting Verif entry using the following query: %s' % (sql))
                DatabaseConnect.delete(sql)
            except Exception as e:
                logging.error("The following error occured in loop for unverified: %s" % (e))
        logger.info('Done for all')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
    finally:
        DatabaseConnect.close_connection()

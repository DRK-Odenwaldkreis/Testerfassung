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
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Cleaning for nit verified appointments started on: %s'%(datetime.datetime.now()))
logger.debug('Starting')

if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        sql = "Delete FROM Voranmeldung_Verif WHERE Updated < (NOW() - INTERVAL 10 MINUTE);"
        logger.debug('Deleting all unverified appointments using the following query: %s' % (sql))
        DatabaseConnect.delete(sql)
        logger.debug('Done for all')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

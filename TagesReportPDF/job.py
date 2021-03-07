#!/usr/bin/python3
# coding=utf-8


# This file is part of DRK Testzentrum.

from os import path
import logging
import sys
import datetime
sys.path.append("..")
from utils.database import Database
from pdfcreator.pdf import PDFgenerator
from utils.sendmail import send_mail_report
from utils.getRequesterMail import get_Mail_from_UserID

logFile = '../../Logs/reportJob.log'
logging.basicConfig(filename=logFile,level=logging.DEBUG,format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Daily Report startet on: %s') %(datetime.datetime.now())
logger.debug('Starting')
dailyReport = False

if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        logger.debug(len(sys.argv))
        sql = ""
        logger.debug('Getting all Events from Yesterday with the following query: %s' % (sql))
        content = DatabaseConnect.read_all(sql)
        logger.debug('Received the following entries: %s' % (str(content)))
        PDF = PDFgenerator(content, requestedDate)
        result = PDF.generate()
        logger.debug('Done')
        if dailyReport:
            send_mail_report(result, datetime.datetime.now().date())
        print(result)
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
        print("Error")

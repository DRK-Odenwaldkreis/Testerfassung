# This file is part of DRK Testfassung.


import pyqrcode
import png
import sys
sys.path.append("..")
from utils.database import Database
from pdfcreator.pdf import PDFgenerator
import datetime
import time
import locale
import logging


logFile = '../../Logs/ticketGeneration.log'
logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Generating Tickets')
logger.debug('Starting')


if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        sql = "Select Vorname, Nachname, Mailadresse, Slot, Stunde, Tag, Token from Voranmeldung where Token is not NULL and Mailsend = 0;"
        content = DatabaseConnect.read_all(sql)
        logger.debug('Received the following recipients: %s' %(str(content)))
        for i in content:
            logger.debug('Received the following entry: %s' %(str(i)))
            PDF = PDFgenerator()
            PDF.creatPDF(i)
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

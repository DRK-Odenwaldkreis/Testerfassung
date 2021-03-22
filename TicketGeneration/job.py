# This file is part of DRK Testfassung.


import pyqrcode
import png
import sys
sys.path.append("..")
from utils.database import Database
from pdfcreator.pdf import PDFgenerator
from utils.sendmail import send_qr_ticket_mail
from utils.slot import get_slot_time
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
        sql = "Select Vorname, Nachname, Mailadresse, Slot, Stunde, Tag, Token, id from Voranmeldung where Token is not NULL and Mailsend = 0;"
        content = DatabaseConnect.read_all(sql)
        logger.debug('Received the following recipients: %s' %(str(content)))
        for i in content:
            logger.debug('Received the following entry: %s' %(str(i)))
            PDF = PDFgenerator()
            filename = PDF.creatPDF(i)
            vorname = i[0]
            nachname = i[1]
            mail = i[2]
            slot = i[3]
            stunde = i[4]
            tag = i[5]
            entry = i[7]
            appointment = get_slot_time(slot,stunde)
            if send_qr_ticket_mail(mail,tag,vorname,nachname,appointment,filename): 
                logger.debug('Mail was succesfully send, closing entry in db')
                sql = "Update Voranmeldung SET Mailsend = 1 WHERE id = %s;" % (entry)
                DatabaseConnect.update(sql)
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

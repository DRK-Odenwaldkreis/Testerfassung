# This file is part of DRK Testfassung.


import pyqrcode
import png
import sys
sys.path.append("..")
from utils.database import Database
from pdfcreator.pdf import PDFgenerator
from utils.sendmail import send_qr_ticket_pre_register_mail
import datetime
import time
import locale
import logging


logFile = '../../Logs/preRegistration.log'
logging.basicConfig(filename=logFile,level=logging.WARNING,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Pre Registration')
logger.debug('Starting')


if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        sql = "Select Voranmeldung.Vorname, Voranmeldung.Nachname, Voranmeldung.Mailadresse, Voranmeldung.Tag, Voranmeldung.Token, Voranmeldung.id from Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id where Token is not NULL and Mailsend = 0 and Termine.Slot is NULL;"
        content = DatabaseConnect.read_all(sql)
        logger.debug('Received the following recipients: %s' %(str(content)))
        for i in content:
            try:
                logger.debug('Received the following entry: %s' %(str(i)))
                vorname = i[0]
                nachname = i[1]
                mail = i[2]
                date = i[3]
                token = i[4]
                entry = i[5]
                PDF = PDFgenerator()
                filename = PDF.creatPDF(i)
                if send_qr_ticket_pre_register_mail(mail,date,vorname,nachname,filename): 
                    logger.debug('Mail was succesfully send, closing entry in db')
                    sql = "Update Voranmeldung SET Mailsend = 1 WHERE id = %s;" % (entry)
                    DatabaseConnect.update(sql)
            except Exception as e:
                logging.error("The following error occured in loop of content: %s" % (e))
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

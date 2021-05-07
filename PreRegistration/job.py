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
logging.basicConfig(filename=logFile,level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Pre Registration')
logger.info('Starting pre registration ticket creation')


if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        sql = "Select Voranmeldung.Vorname, Voranmeldung.Nachname, Voranmeldung.Mailadresse, Voranmeldung.Tag, Voranmeldung.Token, Voranmeldung.id, Station.Ort, Station.Adresse, Termine.opt_station_adresse, Termine.opt_station from Voranmeldung JOIN Termine ON Termine.id=Voranmeldung.Termin_id JOIN Station ON Termine.id_station=Station.id where Voranmeldung.Token is not NULL and Voranmeldung.Mailsend = 0 and Termine.Slot is NULL;"
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
                ort = i[6]
                adress = i[7]
                opt_ort = i[8]
                opt_adress = i[9]
                if len(opt_ort) == 0 and len(opt_adress) == 0:
                    location = str(ort) + ", " + str(adress)
                else:
                    location = str(opt_ort) + "," + str(opt_adress)
                PDF = PDFgenerator()
                filename = PDF.creatPDF(i,location)
                url = "https://testzentrum-odw.de/registration/index.php?cancel=cancel&t=%s&i=%s" % (token,entry)
                if send_qr_ticket_pre_register_mail(mail,date,vorname,nachname,location,filename,url): 
                    logger.debug('Mail was succesfully send, closing entry in db')
                    sql = "Update Voranmeldung SET Mailsend = 1 WHERE id = %s;" % (entry)
                    DatabaseConnect.update(sql)
            except Exception as e:
                logging.error("The following error occured in loop of content: %s" % (e))
        logger.info("Done")
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
    finally:
        DatabaseConnect.close_connection()

# This file is part of DRK Testfassung.


import pyqrcode
import png
from zipfile import ZipFile
import sys
sys.path.append("..")
from utils.database import Database
from utils.getRequesterMail import get_Mail_from_UserID
from utils.sendmail import send_mail_download_sheet
from pdfcreator.pdf import PDFgenerator
import datetime
import time
import locale
import logging


logFile = '../../Logs/loginSheet.log'
logging.basicConfig(filename=logFile,level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Generating of Login Sheet')
logger.info('Starting creation of login sheet')



if __name__ == "__main__":
    try:
        if len(sys.argv) != 5:
            logger.debug(
                'Input parameters are not correct, requester, user, password, station is needed')
            raise Exception
        requester = sys.argv[1]
        user = sys.argv[2]
        password = sys.argv[3]
        station = sys.argv[4]
        PDF = PDFgenerator()
        result = PDF.creatPDF(user, password, station).replace('../../LoginSheet/','')
        logger.info('Done')
        #send_mail_download_sheet(result,get_Mail_from_UserID(requester))
        print(result)
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

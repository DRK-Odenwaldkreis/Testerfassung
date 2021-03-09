# This file is part of DRK Testfassung.


import pyqrcode
import png
from zipfile import ZipFile
import sys
sys.path.append("..")
from utils.database import Database
from pdfcreator.pdf import PDFgenerator
import datetime
import time
import locale
import logging


logFile = '../../Logs/qrgeneration.log'
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Generating QR Codes')
logger.debug('Starting')

def get_codes(amount):
    i=0
    DatabaseConnect = Database()
    sql = "INSERT INTO Kartennummern (Used) VALUES (0);"
    code_list = []
    while i < int(amount):
        id = DatabaseConnect.insert_feedbacked(sql)
        code_list.append(id)
        i+=1
    DatabaseConnect.cursor.close()
    return code_list


if __name__ == "__main__":
    try:
        if len(sys.argv) != 2:
            logger.debug(
                'Input parameters are not correct, amount is needed')
            raise Exception
        amount = sys.argv[1]
        PDF = PDFgenerator()  
        PDF.add_codes(get_codes(amount))
        PDF.creatPDF()
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

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
logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Generating QR Codes')
logger.debug('Starting')

def get_codes(amount):
    i=0
    DatabaseConnect = Database()
    sql = "INSERT INTO Kartennummern (Used) VALUES ('%s')"
    tupel = (0,)
    code_list = []
    while i < int(amount):
        id = DatabaseConnect.insert_feedbacked(sql,tupel)
        code_list.append(id)
    return code_list


if __name__ == "__main__":
    try:
        PDF = PDFgenerator()  
        PDF.add_codes(get_codes(5))
        fileName = PDF.creatPDF()
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

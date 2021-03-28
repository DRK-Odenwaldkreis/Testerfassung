import logging
import locale
import time
import datetime
import sys
import csv
import numpy as np 
from pdfcreator.pdf import PDFgenerator
sys.path.append("..")
from utils.database import Database
from utils.sendmail import send_month_mail_report
from utils.getRequesterMail import get_Leitung_from_StationID


logFile = '../../Logs/MonatsreportJob.log'
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Monatsreporting')
logger.debug('Starting')

if __name__ == "__main__":
    try:
        if len(sys.argv)  == 3:
            send=False
        elif len(sys.argv) == 4:
            send=True
        else:
            logger.debug('Input parameters are not correct, date and/or requested needed')
            raise Exception
        requestedMonth = sys.argv[1]
        requestedYear = sys.argv[2]
        DatabaseConnect = Database()
        sql = "Select SUM(Amount),Teststation,Ort from Abrechnung JOIN Station ON Abrechnung.Teststation = Station.id where MONTH(Abrechnung.Date)=%s and YEAR(Abrechnung.Date)=%s GROUP BY Teststation;" % (requestedMonth,requestedYear)
        content = DatabaseConnect.read_all(sql)
        logger.debug('Received the following entries: %s' %(str(content)))
        PDF = PDFgenerator(content, requestedMonth, requestedYear)
        filename = PDF.generate()
        """if send:
            logger.debug('Sending Mail')
            send_month_mail_report(filename,requestedMonth,requestedYear)"""
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

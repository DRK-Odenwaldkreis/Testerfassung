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
from utils.sendmail import send_mail_report
from utils.getRequesterMail import get_Mail_from_StationID


logFile = '../../Logs/TagesreportJob.log'
logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Tagesreport')
logger.debug('Starting')


def create_PDFs(content, date,station):
    tests = 0
    positiv = 0
    negativ = 0
    unklar = 0
    times = []
    for i in content:
        times.append(i[4].total_seconds()/60)
        ergebnis = i[1]
        if ergebnis == 2:
            negativ += 1
        elif ergebnis == 1:
            positiv += 1
        elif ergebnis == 9:
            unklar += 1
        else:
            pass
    logger.debug('Positive tests: %s' % (str(positiv)))
    logger.debug('Negative tests: %s' % (str(negativ)))
    logger.debug('Unclear tests: %s' % (str(unklar)))
    tests = unklar + negativ + positiv
    logger.debug('Calculated this total number of tests: %s' % (str(tests)))
    pdfcontent = [station,tests, positiv, negativ, unklar,times]
    print(pdfcontent)
    PDF = PDFgenerator(pdfcontent, f"{date}")
    return PDF.generate()

if __name__ == "__main__":
    try:
        if len(sys.argv)  == 2:
            requestedDate = sys.argv[1]
            send=False
        elif len(sys.argv) == 3:
            requestedDate = sys.argv[1]
            send=True
        else:
            logger.debug('Input parameters are not correct, date and/or requested needed')
            raise Exception
        DatabaseConnect = Database()
        sql = "Select Teststation,Ort from Vorgang JOIN Station ON Vorgang.Teststation = Station.id where Ergebniszeitpunkt Between '%s 00:00:00' and '%s 23:59:59' GROUP BY Teststation" % (requestedDate.replace('-', '.'), requestedDate.replace('-', '.'))
        teststationen = DatabaseConnect.read_all(sql)
        sql = "Select id,Ergebnis,Ergebniszeitpunkt,Teststation,TIMEDIFF(Ergebniszeitpunkt,Registrierungszeitpunkt) from Vorgang where Ergebniszeitpunkt Between '%s 00:00:00' and '%s 23:59:59';" % (
            requestedDate.replace('-', '.'), requestedDate.replace('-', '.'))
        logger.debug('Getting all Events for a date with the following query: %s' % (sql))
        exportEvents = DatabaseConnect.read_all(sql)
        logger.debug('Received the following entries: %s' %(str(exportEvents)))
        for station in teststationen:
            filename = create_PDFs(exportEvents, requestedDate, station[1])
            if send:
                logger.debug('Sending Mail')
                send_mail_report(filename,requestedDate,get_Mail_from_StationID(station[0]))
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

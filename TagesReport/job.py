import logging
import locale
import time
import datetime
import sys
import csv
from pdfcreator.pdf import PDFgenerator
sys.path.append("..")
from utils.database import Database
from utils.sendmail import send_mail_report


logFile = '../../Logs/TagesreportJob.log'
logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('CSV Export')
logger.debug('Starting')


def create_PDFs(content, date,station):
    tests = 0
    positiv = 0
    negativ = 0
    unklar = 0
    for i in content:
        if i[8] == station[0]:
            ergebnis = i[7]
            if ergebnis == 0:
                negativ += 1
            elif ergebnis == 1:
                positiv += 1
            else:
                unklar += 1
    pdfcontent = [station[1],tests, positiv, negativ, unklar]
    PDF = PDFgenerator(pdfcontent, f"{date}")
    return PDF.generate()


if __name__ == "__main__":
    try:
        if len(sys.argv)  == 2:
            requestedDate = sys.argv[1]
        elif len(sys.argv) == 3:
            requestedDate = sys.argv[1]
            requester = sys.argv[2]
        else:
            logger.debug('Input parameters are not correct, date and/or requester needed')
            raise Exception
        DatabaseConnect = Database()
        sql = "SELECT id,Ort FROM Station"
        teststationen = DatabaseConnect.read_all(sql)
        sql = "Select id,Nachname,Vorname,Geburtsdatum,Adresse,Telefon,Mailadresse,Ergebnis,Ergebniszeitpunkt,Teststation from Vorgang where Ergebniszeitpunkt Between '%s 00:00:00' and '%s 23:59:59';" % (requestedDate,requestedDate)
        logger.debug('Getting all Events for a date with the following query: %s' % (sql))
        exportEvents = DatabaseConnect.read_all(sql)
        logger.debug('Received the following entries: %s' %(str(exportEvents)))
        filenames = []
        for station in teststationen:
            filenames.append(create_PDFs(exportEvents, requestedDate, station))
        if requester:
            logger.debug('Sending Mail')
            send_mail_report(filenames,requestedDate, requester)
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

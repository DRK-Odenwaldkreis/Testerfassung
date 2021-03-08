import logging
import locale
import time
import datetime
import sys
import csv
from pdfcreator.pdf import PDFgenerator
sys.path.append("..")
from utils.database import Database


logFile = '../../Logs/CSVExportJob.log'
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
    PDF.generate()

        #need to return filenames?


if __name__ == "__main__":
    try:
        print("Test")
        if len(sys.argv) != 2:
            logger.debug('Input parameters are not correct, date needed')
            raise Exception
        requestedDate = sys.argv[1]
        DatabaseConnect = Database()
        sql = "SELECT id,Ort FROM Station"
        teststationen = DatabaseConnect.read_all(sql)
        sql = "Select id,Nachname,Vorname,Geburtsdatum,Adresse,Telefon,Mailadresse,Ergebnis,Ergebniszeitpunkt,Teststation from Vorgang where DATE(Ergebniszeitpunkt)='%s';" % (
            requestedDate)
        logger.debug('Getting all Events for a date with the following query: %s' % (sql))
        exportEvents = DatabaseConnect.read_all(sql)
        logger.debug('Received the following entries: %s' %(str(exportEvents)))
        for station in teststationen:
            create_PDFs(exportEvents, requestedDate,station)
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

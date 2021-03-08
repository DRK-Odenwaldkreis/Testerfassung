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

Teststationen = {1: "Musterstadt", 15: "Breuberg", 18: "Michelstadt", 815: "Würzberg"} #This has to be modified to contain
#all existing testing stations"


def create_CSV(content, day, month, year):
    filename = f"export_{day}-{month}-{year}.csv"
    with open(filename, mode='w', newline='') as csvfile:
        writeEntry = csv.writer(csvfile, delimiter=';')
        writeEntry.writerow(["id", 
                            "Nachname",
                            "Vorname",
                            "Geburtsdatum",
                            "Adresse", 
                            "Telefonnummer", 
                            "Mailadresse", 
                            "Ergebnis", 
                            "Testzeitpunkt",
                            "Teststation"
                             ])
        for i in content:
            writeEntry.writerow(i)
    return filename

def create_PDFs(content, day, month, year):
    for key in Teststationen:
        tests = 0
        positiv = 0
        negativ = 0
        unklar = 0
        for i in content:
            if i[8] == key:
                ergebnis = i[7]
                if ergebnis == 0:
                    negativ += 1
                elif ergebnis ==1:
                    positiv += 1
                else:
                    unklar += 1
        pdfcontent = [(key,Teststationen[key]),tests, positiv, negativ, unklar]   
        PDF = PDFgenerator(pdfcontent, f"{day}.{month}.{year}")
        PDF.generate()  

        #need to return filenames?


if __name__ == "__main__":
    try:
        if len(sys.argv) != 4:
            logger.debug(
                'Input parameters are not correct, Day, Month and Year needed')
            # print('Input parameters are not correct, Day, Month and Year needed')
            raise Exception
        logger.debug(
            f'Was started for the following Date {sys.argv[1]}.{sys.argv[2]}.{sys.argv[3]}')
        # print(f'Was started for the following Date {sys.argv[1]}.{sys.argv[2]}.{sys.argv[3]}')
        requestedDay = sys.argv[1]
        requestedMonth = sys.argv[2]
        requestedYear = sys.argv[3]
        DatabaseConnect = Database()

        sql = f"Select id,Nachname,Vorname,Geburtsdatum,Adresse,Telefon,Mailadresse,Ergebnis,Ergebniszeitpunkt,Teststation from Vorgang where Ergebniszeitpunkt > '{requestedYear}-{requestedMonth}-{requestedDay} 00:00:00' and Ergebniszeitpunkt < '{requestedYear}-{requestedMonth}-{requestedDay} 23:59:59'"
        logger.debug('Getting all Events for a day: %s' % (sql))
        # print('Getting all Events for a day: %s' % (sql))
        exportEvents = DatabaseConnect.read_all(sql)
        #logger.debug('Received the following entries: %s' %
        #             (str(exportEvents)))
        # print('Received the following entries: %s' %
        #              (str(exportEvents)))
        filename = create_CSV(exportEvents, requestedDay, requestedMonth, requestedYear)
        create_PDFs(exportEvents, requestedDay, requestedMonth, requestedYear)
        logger.debug('Done')
        # print("done")
        print(filename.replace('../../Reports', ''))
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
        print("Error")
        # print("The following error occured: %s" % (e))

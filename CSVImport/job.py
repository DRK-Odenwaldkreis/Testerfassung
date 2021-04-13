#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testerfassung.


import logging
import locale
import time
import datetime
import sys
import csv
sys.path.append("..")
from utils.database import Database

logFile = '../../Logs/CSVImportJob.log'
logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('CSV Import')
logger.debug('Starting')

if __name__ == "__main__":
    try:
        DatabaseConnect = Database()
        filename = sys.argv[1]
        with open(filename, newline='') as csvfile:
            next(csvfile)
            for row in csv.reader(csvfile, delimiter=',', quotechar='|'):
                try:
                    #Generate Token
                    #Test that token is unique
                    token = "1234"
                    vorname = row[0]
                    nachname = row[1]
                    wohnort = row[2]
                    strasse = row[3]
                    gebdatum = row[4]
                    #Values as termin_id,tag and mail from input variables
                    sql = "Insert INTO Voranmeldung (Token,Vorname,Nachname,Wohnort,Strasse,Geburtsdatum,Tag,Termin_id,zip_request,Mailadresse) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
                    tupel = (token,vorname,nachname,wohnort,strasse,gebdatum,'2021-04-01',32,1,'testen@familie-bayram.eu',)
                    DatabaseConnect.insert(sql,tupel)
                #sendmail to requester
                except Exception as e:
                    logging.error("The following error occured: %s" % (e))
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

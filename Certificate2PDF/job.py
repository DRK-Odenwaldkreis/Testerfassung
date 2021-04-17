#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

import datetime
from os import path
import logging
import pdfkit
import codecs
import sys
sys.path.append("..")
from utils.database import Database


logFile = '../../Logs/certificateJob.log'
logging.basicConfig(level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Single Certification Job startet on: %s' %(datetime.datetime.now()))
logger.info('Starting single certificate creation Job')

if __name__ == "__main__":
    try:
        if len(sys.argv)  != 2:
            logger.debug('Input parameters are not correct, kartenummer needed')
            raise Exception
        else:
            requestedNumber = sys.argv[1]
            DatabaseConnect = Database()
            sql = "Select Vorname,Nachname,Ergebnis,Registrierungszeitpunkt,Geburtsdatum from Vorgang where Token=%s;"%(requestedNumber)
            requester = DatabaseConnect.read_single(sql)
            print(requester)
            vorname = requester[0]
            nachname = requester[1]
            ergebnis = requester[2]
            date = requester[3].strftime("%d.%m.%Y um %H:%M Uhr")
            geburtsdatum = requester[4]
            if ergebnis == 1:
                inputFile = "../utils/MailLayout/Positive_Result.html"
            elif ergebnis == 2:
                inputFile = "../utils/MailLayout/Negative_Result.html"
            elif ergebnis == 9:
                inputFile = "../utils/MailLayout/Indistinct_Result.html"
            else:
                raise Exception
            layout = open(inputFile, 'r', encoding='utf-8')
            inputContent = layout.read().replace('[[DATE]]', str(date)).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]',str(nachname)).replace('[[GEBDATUM]]',str(geburtsdatum))
            outputFile = "../../Zertifikate/" + str(requestedNumber) + ".pdf" 
            pdfkit.from_string(inputContent, outputFile)
            print(outputFile)
            DatabaseConnect.close_connection()
            logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

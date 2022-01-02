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
logging.basicConfig(filename=logFile,level=logging.INFO,
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
            sql = "Select Vorname,Nachname,Ergebnis,Registrierungszeitpunkt,Geburtsdatum,Testtyp.Name,Testtyp.IsPCR,Vorgang.Customer_key from Vorgang JOIN Testtyp ON Testtyp_id=Testtyp.id where Token=%s;"%(requestedNumber)
            requester = DatabaseConnect.read_single(sql)
            vorname = requester[0]
            nachname = requester[1]
            ergebnis = requester[2]
            date = requester[3].strftime("%d.%m.%Y um %H:%M Uhr")
            geburtsdatum = requester[4]
            manufacturer = requester[5]
            isPCR = requester[6]
            key = requester[7]
            if isPCR == 1:
                testtype = "RT-PCR Labortest"
            else:
                testtype = "SARS-CoV-2 PoC Ag Test"
            if ergebnis == 1:
                inputFile = "../utils/MailLayout/Positive_Result.html"
            elif ergebnis == 2:
                inputFile = "../utils/MailLayout/Negative_Result.html"
            elif ergebnis == 9:
                inputFile = "../utils/MailLayout/Indistinct_Result.html"
            else:
                raise Exception
            qrURL = f'https://www.testzentrum-odw.de/result.php?validate=1&i={requestedNumber}&t={key}&g={geburtsdatum}'
            layout = open(inputFile, 'r', encoding='utf-8')
            inputContent = layout.read().replace('[[DATE]]', str(date)).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]',str(nachname)).replace('[[GEBDATUM]]',str(geburtsdatum)).replace('[[MANUFACTURER]]', str(manufacturer)).replace('[[TESTTYPE]]', str(testtype)).replace('[[QRURL]]', str(qrURL))
            outputFile = "../../Zertifikate/" + str(requestedNumber) + ".pdf" 
            pdfkit.from_string(inputContent, outputFile)
            print(str(requestedNumber) + ".pdf")
            logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
    finally:
        DatabaseConnect.close_connection()

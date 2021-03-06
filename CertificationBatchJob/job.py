#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

import datetime
from os import path
import os
import logging
import pdfkit
import codecs
from zipfile import ZipFile
import sys
sys.path.append("..")
from utils.database import Database
from utils.getRequesterMail import get_Mail_from_UserID
from utils.sendmail import send_mail_download_certificate
from utils.token import generate_token


logFile = '../../Logs/certificateJob.log'
logging.basicConfig(filename=logFile,level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Certification Job startet on: %s' %(datetime.datetime.now()))
logger.info('Starting certificate creation Job')

if __name__ == "__main__":
    try:
        if len(sys.argv)  != 4:
            logger.debug('Input parameters are not correct, date station and requested needed')
            raise Exception
        else:
            requestedDate = sys.argv[1]
            requestedStation = sys.argv[2]
            requester = sys.argv[3]
            token = generate_token(32)
            dir = "../../Zertifikate/" + token + '/'
            os.mkdir(dir)
            zipFilename = str(dir) + 'Zertifikate_' + str(requestedDate) + '_Station_' + str(requestedStation) + '.zip'
            zipObj = ZipFile(zipFilename, 'w')
            DatabaseConnect = Database()
            sql = "Select Nachname, Vorname, Ergebniszeitpunkt, Geburtsdatum, Ergebnis, Testtyp.Name, Testtyp.IsPCR from Vorgang JOIN Testtyp ON Testtyp_id=Testtyp.id where zip_request=1 and Ergebnis !=5 and Teststation=%s and Ergebniszeitpunkt Between '%s 00:00:00' and '%s 23:59:59';"%(requestedStation,requestedDate,requestedDate)
            content = DatabaseConnect.read_all(sql)
            for i in content:
                try:
                    nachname = i[0]
                    vorname = i[1]
                    date = i[2]
                    geburtsdatum = i[3]
                    ergebnis = i[4]
                    manufacturer = i[5]
                    isPCR = i[6]
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
                    layout = open(inputFile, 'r', encoding='utf-8')
                    inputContent = layout.read().replace('[[DATE]]', str(date)).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]',str(nachname)).replace('[[GEBDATUM]]',str(geburtsdatum)).replace('[[MANUFACTURER]]', str(manufacturer)).replace('[[TESTTYPE]]', str(testtype))
                    outputFile = str(dir) + str(vorname).replace(" ","") + "_" + str(nachname).replace(" ","") + "_" + date.strftime("%Y-%m-%d") + ".pdf" 
                    pdfkit.from_string(inputContent, outputFile)
                    zipObj.write(outputFile, outputFile.replace(dir, 'Zertifikat_'))
                except Exception as e:
                    logging.error("The following error occured: %s" % (e))
            zipObj.close()
            if send_mail_download_certificate('Zertifikate_' + str(requestedDate) + '_Station_' + str(requestedStation) + '.zip', token, get_Mail_from_UserID(requester)):
                            sql = "Update Vorgang SET zip_lock=0 where zip_request=1 and Ergebnis !=5 and Teststation=%s and Ergebniszeitpunkt Between '%s 00:00:00' and '%s 23:59:59';"%(requestedStation,requestedDate,requestedDate)
                            DatabaseConnect.update(sql)
            logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
    finally:
        DatabaseConnect.close_connection()

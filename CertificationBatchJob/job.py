#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

import datetime
from os import path
import logging
import pdfkit
import codecs
from zipfile import ZipFile
import sys
sys.path.append("..")
from utils.database import Database
from utils.getRequesterMail import get_Mail_from_UserID
from utils.sendmail import send_mail_download


logFile = '../../Logs/certificateJob.log'
logging.basicConfig(filename=logFile,level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Certification Job startet on: %s' %(datetime.datetime.now()))
logger.info('Starting certificate creation Job')

if __name__ == "__main__":
    try:
        if len(sys.argv)  != 4:
            logger.debug('Input parameters are not correct, station,date and requested needed')
            raise Exception
        else:
            requestedDate = sys.argv[1]
            requestedStation = sys.argv[2]
            requester = sys.argv[3]
            zipFilename = '../../Zertifikate/Zertifikate_' + str(requestedDate) + '_Station_' + str(requestedStation) + '.zip'
            zipObj = ZipFile(zipFilename, 'w')
            DatabaseConnect = Database()
            sql = "Select Nachname, Vorname, Ergebniszeitpunkt, Geburtsdatum, Ergebnis from Vorgang where zip_request=1 and Ergebnis !=5 and Teststation=%s and Ergebniszeitpunkt Between '%s 00:00:00' and '%s 23:59:59';"%(requestedStation,requestedDate,requestedDate)
            content = DatabaseConnect.read_all(sql)
            print(content)
            for i in content:
                try:
                    print(i)
                    nachname = i[0]
                    vorname = i[1]
                    date = i[2]
                    geburtsdatum = i[3]
                    ergebnis = i[4]
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
                    outputFile = "../../Zertifikate/" + str(vorname).replace(" ","") + "_" + str(nachname).replace(" ","") + "_" + date.strftime("%Y-%m-%d") + ".pdf" 
                    pdfkit.from_string(inputContent, outputFile)
                    zipObj.write(outputFile, outputFile.replace('../../Zertifikate/', ''))
                except Exception as e:
                    logging.error("The following error occured: %s" % (e))
            zipObj.close()
            send_mail_download(zipFilename, get_Mail_from_UserID(requester))
            logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

import datetime
from os import path
import logging
from pdfkit import from_url as pdf_from_url
import codecs
import sys
sys.path.append("..")
from utils.database import Database


logFile = '../../Logs/certificateJob.log'
logging.basicConfig(filename=logFile,level=logging.WARNING,
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
            sql = "Select Vorgang.Customer_key,Vorgang.Geburtsdatum from Vorgang where Token=%s;"%(requestedNumber)
            requester = DatabaseConnect.read_single(sql)
            key = requester[0]
            geburtsdatum = requester[1]
            url = f'https://www.testzentrum-odw.de/result.php?validate=1&i={requestedNumber}&t={key}&g={geburtsdatum}'
            outputFile = "../../Zertifikate/" + str(requestedNumber) + ".pdf" 
            options = {'disable-external-links': True,'page-size':'A4', 'dpi':300}
            pdf_from_url(url, outputFile,options=options)
            print(str(requestedNumber) + ".pdf")
            logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
    finally:
        DatabaseConnect.close_connection()

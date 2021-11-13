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
import createLabel

logFile = '../../Logs/labelJob.log'
logging.basicConfig(filename=logFile,level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Label Job startet on: %s' %(datetime.datetime.now()))
logger.info('Starting Label Job')

if __name__ == "__main__":
    try:
        if len(sys.argv)  != 2:
            logger.debug('Input parameters are not correct, kartenummer needed')
            raise Exception
        else:
            requestedNumber = sys.argv[1]
            DatabaseConnect = Database()
            sql = "Select Vorname,Nachname,Registrierungszeitpunkt,Geburtsdatum,Adresse,Wohnort,Token from Vorgang where Token=%s;"%(requestedNumber)
            content = DatabaseConnect.read_single(sql)
            logger.debug('Received the following entries: %s' %(str(content)))
            filename = createLabel.createLabel(content)
            print(filename)
            logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
    finally:
        DatabaseConnect.close_connection()

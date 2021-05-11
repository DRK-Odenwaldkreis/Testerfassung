#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

import datetime
import locale
from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
from utils.sendmail import send_linked_result
from utils.sendmail import send_new_entry
from utils.sendmail import send_notification
from CWARequest import notify

#locale.setlocale(locale.LC_ALL, 'de_DE')

logFile = '../../Logs/CWAJob.log'
logging.basicConfig(filename=logFile,level=logging.INFO,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Corona Warn Notification startet on: %s' %(datetime.datetime.now()))
logger.info('Starting Rendition Job')

if __name__ == "__main__":
    try:
        sql = "Select Geburtsdatum,Vorname,Nachname,Registrierungszeitpunkt,Token,id,salt,Ergebnis,CWA_lock,CWA_request from Vorgang where Ergebnis !=5 and CWA_request!=0 and ((CWA_lock < 6 and CWA_lock != 0) or CWA_lock is NULL);"
        DatabaseConnect = Database()
        logger.debug('Checking for new results, using the following query: %s' % (sql))
        content = DatabaseConnect.read_all(sql)
        logger.debug(
            'Received the following content: %s' % (content))
        if len(content) > 0:
            logger.debug(
                'Content contains infos')
            for i in content:
                try:
                    gebDatum = i[0]
                    vorname = i[1]
                    nachname = i[2]
                    timestamp = int(i[3].timestamp())
                    testid = i[4]
                    salt = i[6]
                    entry = i[5]
                    result = i[7]
                    failedAttempts=i[8]
                    requestType=i[9]
                    if failedAttempts is None:
                        failedAttempts=0
                    if requestType == 1:
                        hash_string = str(gebDatum) + "#" + str(vorname) + "#" + str(nachname) + "#" + str(timestamp) + "#" + str(testid) + "#" + str(salt)
                    elif requestType == 2:
                        hash_string = str(timestamp) + "#" + str(salt)
                    else:
                        continue
                    notified = notify(hash=hash_string,result=result)
                    if notified:
                        logger.debug('Corona Warn Nofification was succesfully send, closing entry in db')
                        sql = "Update Vorgang SET CWA_lock = 0 WHERE id = %s;" % (entry)
                        DatabaseConnect.update(sql)
                    else:
                        failedAttempts += 1
                        sql = "Update Vorgang SET CWA_lock = %s WHERE id = %s;" % (failedAttempts,entry)
                        DatabaseConnect.update(sql)
                except Exception as e:
                    logging.error("The following error occured in loop of content: %s" % (e))
        else:
            logger.debug('Nothing to do')
        logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

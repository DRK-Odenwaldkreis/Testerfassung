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
logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Corona Warn Notification startet on: %s' %(datetime.datetime.now()))
logger.info('Starting Rendition Job')

if __name__ == "__main__":
    try:
        sql = "Select id,uuid,Ergebnis,TransmissionPath,TransmissionStatus,FailedAttempts from Vorgang2 where Ergebnis is not NULL and FailedAttempts < 6;"
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
                    result = i[2]
                    uuid = i[1]
                    entry = i[0]
                    failedAttempts = i[5]
                    notified = notify(uuid=uuid,result=result)
                    if notified:
                        logger.debug('Corona Warn Nofification was succesfully send, closing entry in db')
                        sql = "Update Vorgang2 SET failedAttempts = 0 WHERE id = %s;" % (entry)
                        DatabaseConnect.update(sql)
                    else:
                        failedAttempts += 1
                        sql = "Update Vorgang2 SET failedAttempts = %s WHERE id = %s;" % (failedAttempts,entry)
                        DatabaseConnect.update(sql)
                except Exception as e:
                    logging.error("The following error occured in loop of content: %s" % (e))
        else:
            logger.debug('Nothing to do')
        logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

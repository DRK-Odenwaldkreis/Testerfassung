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
from utils.sendmail import send_new_entry

#locale.setlocale(locale.LC_ALL, 'de_DE')

logFile = '../../Logs/rotationJob.log'
logging.basicConfig(filename=logFile,level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Rendition Job startet on: %s' %(datetime.datetime.now()))
logger.info('Starting GA Rendition Job')

if __name__ == "__main__":
    try:
        if len(sys.argv) == 2:
            logger.debug('Input parameters are with station id')
            stationID = sys.argv[1]
            sql = "Select id,gaMail_lock from Vorgang where Ergebnis = 1 and ((gaMail_lock < 10 and gaMail_lock != 0) or gaMail_lock is NULL) and Teststation = %s;" % (stationID)
        else:
            logger.debug('Checking all stations')
            sql = "Select id, gaMail_lock from Vorgang where Ergebnis = 1 and ((gaMail_lock < 10 and gaMail_lock != 0) or gaMail_lock is NULL);"
        DatabaseConnect = Database()
        logger.debug('Checking for new positive results, using the following query: %s' % (sql))
        content = DatabaseConnect.read_all(sql)
        logger.debug(
            'Received the following content: %s' % (content))
        if len(content) > 0:
            logger.debug('Content contains infos')
            try:
                date = datetime.datetime.now().strftime("%d.%m.%Y um %H:%M Uhr")
                transmission = send_new_entry(date)
                logger.debug('Checking whether mail was send properly and closing db entry')
                if transmission:
                    listIds = []
                    for i in content:
                        listIds.append(i[0])
                    logger.debug('Mail was succesfully send, closing entry in db')
                    sql = "Update Vorgang SET gaMail_lock=0 WHERE id IN %s;" % (str(tuple(listIds)))
                    DatabaseConnect.update(sql)
                else:
                    logger.debug('mail was not send properly updating gaMail_lock')
                    for i in content:
                        gaMail_lock = i[1]
                        testID = i[0]
                        if gaMail_lock is None:
                            gaMail_lock=0
                        gaMail_lock +=1
                        sql = "Update Vorgang SET gaMail_lock = %s WHERE id = %s;" % (gaMail_lock,testID)
                        DatabaseConnect.update(sql)
            except Exception as e:
                logging.error("The following error occured in loop of content: %s" % (e))
        else:
            logger.debug('Nothing to do')
        logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
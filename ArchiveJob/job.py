#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.

from os import path
import logging
import sys
sys.path.append("..")
from utils.database import Database
import datetime

logFile = '../../Logs/archiveJob.log'
logging.basicConfig(filename=logFile,level=logging.INFO,format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Archieve job stated: %s'%(datetime.datetime.now()))
logger.info('Starting Archive')

if __name__ == "__main__":
    try:
        if len(sys.argv) == 2:
            requestedDate = datetime.datetime.strptime(sys.argv[1], '%Y-%m-%d').date()
        else:
            logger.debug(
                'Input parameters are not correct, date needed')
            raise Exception
        DatabaseConnect = Database()
        sql = "Select Vorgang.id, Teststation, Token, Registrierungszeitpunkt,Vorgang.CWA_request, Vorgang.reg_type, Testtyp.id, Testtyp.IsPCR from Vorgang JOIN Testtyp ON Vorgang.Testtyp_id=Testtyp.id where (Ergebnis != 1 and DATE(Registrierungszeitpunkt) <= '%s' and Testtyp.IsPCR=0) or (Ergebnis != 1 and DATE(Registrierungszeitpunkt) <= '%s' and Testtyp.IsPCR=1) or (Ergebnis = 1 and DATE(Registrierungszeitpunkt) <= '%s');" % (requestedDate-datetime.timedelta(days=2),requestedDate-datetime.timedelta(days=7),requestedDate-datetime.timedelta(days=90))
        logger.debug('Getting all Events for a date with the following query: %s' % (sql))
        deleteCanidate = DatabaseConnect.read_all(sql)
        for i in deleteCanidate:
            try:
                sql = "INSERT INTO Archive (TestNr, Station, Token, Registrierungszeitpunkt, CWA_request, reg_type) VALUES (%s,%s,%s,%s,%s,%s);"
                tupel = (i[0],i[1],i[2],i[3],i[4],i[5])
                if DatabaseConnect.insert(sql,tupel):
                    sql = "Delete from Vorgang where id=%s;"%(i[0])
                    DatabaseConnect.delete(sql)
            except Exception as e:
                logging.error("The following error occured in loop of delete canidates: %s" % (e))
        logger.info('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))
    finally:
        DatabaseConnect.close_connection()

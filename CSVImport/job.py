#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testerfassung.


import logging
import locale
import time
import datetime
import sys
import os
import csv
import random
sys.path.append("..")
from utils.database import Database
from utils.getRequesterMail import get_Mail_from_UserID

logFile = '../../Logs/CSVImportJob.log'
logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('CSV Import')
logger.debug('Starting')

def generate_token():
    x = 'P' + ''.join(random.choice('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz') for i in range(8))
    return x

if __name__ == "__main__":
    try:
        if len(sys.argv)  != 4:
            logger.debug('Input parameters are not correct, filename,termin_id and requested needed')
            raise Exception
        else:
            DatabaseConnect = Database()
            filename = sys.argv[1]
            termin_id = sys.argv[2]
            requester = sys.argv[3]
            mail = get_Mail_from_UserID(requester)
            sql = "Select Tag from Termine where id=%s" % (termin_id)
            tag = DatabaseConnect.read_single(sql)[0]
            with open(filename, newline='') as csvfile:
                next(csvfile)
                counter = 0
                for row in csv.reader(csvfile, delimiter=';', quotechar='|'):
                    try:
                        logger.debug('Processing the following entry: %s' % (row))
                        tokenUniqueness = False
                        while not tokenUniqueness:
                            token = generate_token()
                            sql = "Select id from Voranmeldung where Token='%s'" % (token)
                            reply = DatabaseConnect.read_single(sql)
                            if reply is None:
                                tokenUniqueness = True
                                logging.debug("Token is unique")
                            else:
                                logging.warning("Token is not unique")
                        vorname = row[0]
                        nachname = row[1]
                        wohnort = row[2]
                        adresse = row[3]
                        gebdatum = row[4]
                        sql = "Insert INTO Voranmeldung (Token,Vorname,Nachname,Wohnort,Adresse,Geburtsdatum,Tag,Termin_id,zip_request,Reminded,Mailadresse) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"
                        tupel = (token,vorname,nachname,wohnort,adresse,gebdatum,tag,int(termin_id),1,1,mail,)
                        if DatabaseConnect.insert(sql,tupel):
                            counter +=1
                            logger.debug('Adding +1 to counter')
                    except Exception as e:
                        logging.error("The following error occured: %s" % (e))
        print(counter)
        os.remove(filename)
        DatabaseConnect.close_connection()
        logger.debug('Done')
    except Exception as e:
        logging.error("The following error occured: %s" % (e))

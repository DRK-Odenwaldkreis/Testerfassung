#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testerfassung.
from zipfile import ZipFile

import smtplib
import datetime
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.base import MIMEBase
from email import encoders
import logging
import sys
import os
sys.path.append("..")

from utils.readconfig import read_config

logger = logging.getLogger('Send Mail')
logger.debug('Starting')

FROM_EMAIL = read_config("Mail", "FROM_EMAIL")
TO_EMAIL = read_config("Mail", "TO_EMAIL")
SMTP_SERVER = read_config("Mail", "SMTP_SERVER")
SMTP_USERNAME = read_config("Mail", "SMTP_USERNAME")
SMTP_PASSWORD = read_config("Mail", "SMTP_PASSWORD")

def send_mail_gesundheitsamt(filename, date):
    try:
        zip_password = read_config("Gesundheitsamt", "ZIP_PASSWORD")
        logging.debug("Receviced the following filename %s to be sent." % (filename))
        message = MIMEMultipart()
        with open('../utils/MailLayout/NewCSV.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DAY]]', str(date))
        message.attach(MIMEText(messageContent, 'html'))
        recipients = [read_config("Gesundheitsamt", "TO_EMAIL")]
        message['Subject'] = "Neue Meldeliste aus dem Testzentrum für: %s" % (str(day))
        message['From'] = 'xxx'
        message['reply-to'] = 'xxx'
        message['Cc'] = 'xxx'
        message['To'] = ", ".join(recipients)
        filenameRaw = filename
        filename = '../../Reports/' + str(filenameRaw) + '_' + 'date'
        zipFilename = '../../Reports/' + str(filename) + '_' + 'date'
        zipObj = ZipFile(zipFilename, 'w')
        zipObj.setpassword(zip_password)
        zipObj.write(filename, filename.replace('../../Reports/', 'CSVNachweis_'))
        zipObj.close()
        files = []
        files.append(filename)
        for item in files:
            attachment = open(item, 'rb')
            part = MIMEBase('application', 'octet-stream')
            part.set_payload((attachment).read())
            encoders.encode_base64(part)
            part.add_header(
                'Content-Disposition', "attachment; filename= " + item.replace('../../Reports/', ''))
            message.attach(part)
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        smtp.send_message(message)
        logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail gesundheitsamt: %s" % (err))
        return False


def send_mail_report(filenames, day):
    try:
        logging.debug("Receviced the following filename %s to be sent." % (filenames))
        message = MIMEMultipart()
        with open('../utils/MailLayout/NewReport.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DAY]]', str(day))
        message.attach(MIMEText(messageContent, 'html'))
        recipients = ['x@y.de', 'y@z.de']
        message['Subject'] = "Neue Tagesreport für: %s" % (str(day))
        message['From'] = 'xxx'
        message['reply-to'] = 'xxx'
        message['Cc'] = 'xxx'
        message['To'] = ", ".join(recipients)
        files = []
        for i in filenames:
            filenameRaw = i
            i = '../../Reports/' + str(filenameRaw)
            files.append(i)
        for item in files:
            attachment = open(item, 'rb')
            part = MIMEBase('application', 'octet-stream')
            part.set_payload((attachment).read())
            encoders.encode_base64(part)
            part.add_header(
                'Content-Disposition', "attachment; filename= " + item.replace('../../Reports/', ''))
            message.attach(part)
        smtp = smtplib.SMTP(SMTP_SERVER,port=587)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        smtp.send_message(message)
        logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail download: %s" % (err))
        return False


def send_positive_result(vorname, nachname, mail, date):
    try:
        logging.debug("Receviced the following recipient" % (mail))
        message = MIMEMultipart()
        with open('../utils/MailLayout/Positive_Result.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', str(date)).replace(
            '[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]', str(nachname))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Ergebis Ihres Tests liegt vor"
        message['From'] = 'xxx'
        message['reply-to'] = 'xxx'
        message['To'] = mail
        smtp = smtplib.SMTP(SMTP_SERVER,port=587)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        smtp.send_message(message)
        logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail download: %s" % (err))
        return False


def send_negative_result(vorname, nachname, mail, date):
    try:
        logging.debug("Receviced the following recipient" % (mail))
        message = MIMEMultipart()
        with open('../utils/MailLayout/Negative_Result.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', str(date)).replace(
            '[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]', str(nachname))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Ergebis Ihres Tests liegt vor"
        message['From'] = 'xxx'
        message['reply-to'] = 'xxx'
        message['To'] = mail
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        smtp.send_message(message)
        logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail download: %s" % (err))
        return False


def send_indistinct_result(vorname, nachname, mail, date):
    try:
        logging.debug("Receviced the following recipient" % (mail))
        message = MIMEMultipart()
        with open('../utils/MailLayout/Indistinct_Result.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', str(date)).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]', str(nachname))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Ergebis Ihres Tests liegt vor"
        message['From'] = 'xxx'
        message['reply-to'] = 'xxx'
        message['To'] = mail
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        smtp.send_message(message)
        logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail download: %s" % (err))
        return False


def send_test_mail():
    try:
        start = datetime.datetime.now()
        logging.debug("Started sending Mail: %s" % (start))
        message = MIMEMultipart()
        with open('../utils/MailLayout/Negative_Result.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]',str(datetime.datetime.now())).replace('[[VORNAME]]', 'Murat').replace('[[NACHNAME]]', 'Bayram')
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Ergebis Ihres Tests liegt vor"
        message['From'] = 'support@impfzentrum-odw.de'
        message['reply-to'] = 'support@impfzentrum-odw.de'
        message['To'] = 'testzentrum@familie-bayram.eu'
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        smtp.send_message(message)
        logging.debug("Mail was send")
        smtp.quit()
        end = datetime.datetime.now()
        logging.debug("Finished Sending Mail: %s" %(end))
        logging.debug("Duration was: %s" % (str(end-start)))
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail download: %s" % (err))
        return False

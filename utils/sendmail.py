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
from utils.month import monthInt_to_string

logger = logging.getLogger('Send Mail')
logger.debug('Starting')

FROM_EMAIL = read_config("Mail", "FROM_EMAIL")
TO_EMAIL = read_config("Mail", "TO_EMAIL")
SMTP_SERVER = read_config("Mail", "SMTP_SERVER")
SMTP_USERNAME = read_config("Mail", "SMTP_USERNAME")
SMTP_PASSWORD = read_config("Mail", "SMTP_PASSWORD")
GESUNDHEITSAMT = read_config("Mail", "GESUNDHEITSAMT")
simulationMode = 0


def send_mail_report(filenames, day, recipients):
    try:
        logging.debug(
            "Receviced the following filename %s to be sent." % (filenames))
        message = MIMEMultipart()
        with open('../utils/MailLayout/NewReport.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DAY]]', str(day))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Neuer Tagesreport für: %s" % (str(day))
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['Cc'] = 'testzentrum@drk-odenwaldkreis.de, info@testzentrum-odenwald.de'
        message['To'] = ", ".join(recipients)
        files = [filenames]
        for item in files:
            attachment = open(item, 'rb')
            part = MIMEBase('application', 'octet-stream')
            part.set_payload((attachment).read())
            encoders.encode_base64(part)
            part.add_header(
                'Content-Disposition', "attachment; filename= " + item.replace('../../Reports/', ''))
            message.attach(part)
        logging.debug("Starting SMTP Connection")
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail report: %s" % (err))
        return False

def send_month_mail_report(filenames, month, year):
    try:
        logging.debug(
            "Receviced the following filename %s to be sent." % (filenames))
        message = MIMEMultipart()
        with open('../utils/MailLayout/NewMonthReport.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[MONTH]]', str(monthInt_to_string(int(month))).replace('[[YEAR]]', str(year))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Neuer Report für den Monat: %s, %s" % (str(monthInt_to_string(int(month))),str(year))
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['To'] = 'testzentrum@drk-odenwaldkreis.de, info@testzentrum-odenwald.de'
        files = [filenames]
        for item in files:
            attachment = open(item, 'rb')
            part = MIMEBase('application', 'octet-stream')
            part.set_payload((attachment).read())
            encoders.encode_base64(part)
            part.add_header(
                'Content-Disposition', "attachment; filename= " + item.replace('../../Reports/', ''))
            message.attach(part)
        logging.debug("Starting SMTP Connection")
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail report: %s" % (err))
        return False


def send_csv_report(filename, day):
    try:
        logging.debug("Receviced the following filename %s to be sent." % (filename))
        message = MIMEMultipart()
        url = 'https://testzentrum-odw.de/download.php?file=' + str(filename)
        with open('../utils/MailLayout/NewCSVReport.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace(
            '[[DAY]]', str(day)).replace('[[LINK]]', str(url))
        message.attach(MIMEText(messageContent, 'html'))
        recipients = ['','','']
        message['Subject'] = "Neuer CSV Export für: %s" % (str(day))
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['To'] = ", ".join(recipients)
        logging.debug("Starting SMTP Connection")
        smtp = smtplib.SMTP(SMTP_SERVER,port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send csv report mail: %s" % (err))
        return False


def send_positive_result(vorname, nachname, mail, date, geburtsdatum):
    try:
        logging.debug("Receviced the following recipient %s" % (mail))
        message = MIMEMultipart()
        with open('../utils/MailLayout/Positive_Result.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', str(date)).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]',str(nachname)).replace('[[GEBDATUM',str(geburtsdatum))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Ergebnis Ihres Tests liegt vor"
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['To'] = mail
        files = ['../utils/Share/2021-03-11Anhang_Gesundheitsamt.pdf',
                 '../utils/Share/HMSI-Informationen.pdf']
        for item in files:
            attachment = open(item, 'rb')
            part = MIMEBase('application', 'octet-stream')
            part.set_payload((attachment).read())
            encoders.encode_base64(part)
            part.add_header(
                'Content-Disposition', "attachment; filename= " + item.replace('../utils/Share/', ''))
            message.attach(part)
        logging.debug("Starting SMTP Connection")
        smtp = smtplib.SMTP(SMTP_SERVER,port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send positive mail: %s" % (err))
        return False


def send_notification(vorname, nachname, date):
    try:
        message = MIMEMultipart()
        with open('../utils/MailLayout/Notification.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', str(date)).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]', str(nachname))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Es liegt eine Meldung vor die nicht zugeordnet werden kann."
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['To'] = FROM_EMAIL
        logging.debug("Starting SMTP Connection")
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in notification: %s" % (err))
        return False


def send_new_entry(date):
    try:
        message = MIMEMultipart()
        with open('../utils/MailLayout/NewEntry.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', str(date))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Es liegt eine neue Positivmeldung vor."
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['To'] = GESUNDHEITSAMT
        logging.debug("Starting SMTP Connection")
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send new entry: %s" % (err))
        return False


def send_negative_result(vorname, nachname, mail, date, geburtsdatum):
    try:
        logging.debug("Receviced the following recipient %s" % (mail))
        message = MIMEMultipart()
        with open('../utils/MailLayout/Negative_Result.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', str(date)).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]', str(nachname)).replace('[[GEBDATUM',str(geburtsdatum))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Ergebnis Ihres Tests liegt vor"
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['To'] = mail
        logging.debug("Starting SMTP Connection")
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send negative mail: %s" % (err))
        return False


def send_indistinct_result(vorname, nachname, mail, date, geburtsdatum):
    try:
        logging.debug("Receviced the following recipient %s" % (mail))
        message = MIMEMultipart()
        with open('../utils/MailLayout/Indistinct_Result.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', str(date)).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]', str(nachname)).replace('[[GEBDATUM',str(geburtsdatum))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Ergebnis Ihres Tests liegt vor"
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['To'] = mail
        logging.debug("Starting SMTP Connection")
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send indistinct mail: %s" % (err))
        return False


def send_mail_reminder(recipient, date, vorname, nachname, appointment, url, filename):
    try:
        logging.debug("Receviced the following recipient: %s to be sent to." % (
            recipient))
        message = MIMEMultipart()
        with open('../utils/MailLayout/Reminder.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', date.strftime("%d.%m.%Y")).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]', str(nachname)).replace('[[SLOT]]', str(appointment)).replace('[[LINK]]', str(url))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Erinnerung an Termin %s im Testzentrum des Odenwaldkreis am %s" % (str(appointment), str(date))
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['To'] = recipient
        files = [filename]
        for item in files:
            attachment = open(item, 'rb')
            part = MIMEBase('application', 'octet-stream')
            part.set_payload((attachment).read())
            encoders.encode_base64(part)
            part.add_header(
                'Content-Disposition', "attachment; filename= " + item.replace('../../Tickets/', ''))
            message.attach(part)
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail reminder: %s" % (err))
        return False

def send_qr_ticket_mail(recipient, date, vorname, nachname, appointment, filename, url):
    try:
        logging.debug("Receviced the following recipient: %s to be sent to." % (
            recipient))
        message = MIMEMultipart()
        with open('../utils/MailLayout/QRTicket.html', encoding='utf-8') as f:
            fileContent = f.read()
        messageContent = fileContent.replace('[[DATE]]', date.strftime("%d.%m.%Y")).replace('[[VORNAME]]', str(vorname)).replace('[[NACHNAME]]', str(nachname)).replace('[[SLOT]]', str(appointment)).replace('[[LINK]]', str(url))
        message.attach(MIMEText(messageContent, 'html'))
        message['Subject'] = "Persönliches Testticket für den Termin um %s im Testzentrum des Odenwaldkreis am %s" % (str(appointment), str(date))
        message['From'] = FROM_EMAIL
        message['reply-to'] = FROM_EMAIL
        message['To'] = recipient
        files = [filename]
        for item in files:
            attachment = open(item, 'rb')
            part = MIMEBase('application', 'octet-stream')
            part.set_payload((attachment).read())
            encoders.encode_base64(part)
            part.add_header(
                'Content-Disposition', "attachment; filename= " + item.replace('../../Tickets/', ''))
            message.attach(part)
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
        smtp.set_debuglevel(True)
        smtp.starttls()
        smtp.login(SMTP_USERNAME, SMTP_PASSWORD)
        if simulationMode == 0:
            logging.debug("Going to send message")
            smtp.send_message(message)
            logging.debug("Mail was send")
        smtp.quit()
        return True
    except Exception as err:
        logging.error(
            "The following error occured in send mail qr ticket: %s" % (err))
        return False


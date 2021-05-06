
from zipfile import ZipFile

import smtplib
import datetime
from email.mime.text import MIMEText
from email.mime.multipart import MIMEMultipart
from email.mime.base import MIMEBase
from email import encoders
from email.message import EmailMessage
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

def test():
    try:
        message = EmailMessage()
        text = "Hallo, thats a text with link"
        message.set_content(text)
        message['Subject'] = "Ergebnis Ihres Tests liegt vor"
        message['From'] = "Testzentrum des DRK Odenwaldkreis" + f' <{FROM_EMAIL}>'
        message['Reply-To'] = FROM_EMAIL
        message['To'] = 'murat@familie-bayram.de'
        logging.debug("Starting SMTP Connection")
        smtp = smtplib.SMTP(SMTP_SERVER, port=587)
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
            "The following error occured in send linked mail: %s" % (err))
        return False
test()
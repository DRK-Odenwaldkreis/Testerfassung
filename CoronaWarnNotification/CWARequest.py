
from base64 import b64encode
import logging
import requests
import json
import jsonify
import os
import sys
import datetime
sys.path.append("..")

from utils.readconfig import read_config

payload = {}
headers = {'Content-Type': 'application/json','Accept': 'application/json'}
url = read_config("CWA", "url")

logger = logging.getLogger('Corona Warn Request: %s' %(datetime.datetime.now()))
logger.info('Starting CWA Request')

def notify(uuid,result):
    try:
        if result == 1:
            payload['result'] = 6
        elif result == 2:
            payload['result'] = 7
        elif result == 9:
            payload['result'] = 8
        payload['id'] = str(b64encode(uuid.encode("utf-8")),"utf-8")
        response = requests.request("POST", url, headers=headers, data = json.dumps(payload))
        logger.debug('Response from request: ' + str(response.text))
        logger.debug('Response from request with code : ' + str(response.status_code))
        if response.status_code == 204:
            logger.debug('CWA successfull notfied')
            return True
        else:
            logger.error('Receiving following status code %s'%(str(response.status_code)))
            return False
    except Exception as e:
        logger.error('The following error occured: %s' % (str(e)))
        return False
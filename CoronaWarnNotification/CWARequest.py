
import logging
import hashlib
import requests
import json
import jsonify
import os
import sys
import datetime
sys.path.append("..")
clientCrt = "/home/murat/Certificates/CWA-PRD.cer"
clientKey = "/home/murat/Certificates/plain-prd.key"

logger = logging.getLogger('Corona Warn Request: %s' %(datetime.datetime.now()))
logger.info('Starting CWA Request')

headers = {'Content-Type': 'application/json','Accept': 'application/json'}

def notify(hash,result):
    try:
        url = "https://quicktest-result.coronawarn.app/api/v1/quicktest/results"
        hash_string = str(hash)
        sha_signature = hashlib.sha256(hash_string.encode()).hexdigest()
        payload = {}
        entry = {}
        entry["id"]=sha_signature
        if result == 1:
            entry['result'] = 7
        elif result == 2:
            entry['result'] = 6
        elif result == 9:
            entry['result'] = 8
        array = []
        array.append(entry)
        payload["testResults"] = array
        response = requests.request("POST", url, headers=headers, data = json.dumps(payload),cert=(clientCrt, clientKey))
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
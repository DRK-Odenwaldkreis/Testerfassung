from time import sleep
import logging
from argparse import ArgumentParser
from os import urandom as random_bytes # This is test, we don't need strong crypto
import random
import string
from cryptography.hazmat.primitives.serialization import load_pem_public_key
from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
from cryptography.hazmat.primitives import padding, hashes
from cryptography.hazmat.primitives.asymmetric import padding as asym_padding
from cryptography.hazmat.backends import default_backend
from hashlib import sha256
import json
import cbor2
import requests
from base64 import b64encode
from binascii import hexlify
from datetime import datetime, timezone, timedelta
from time import time, sleep
import sys
sys.path.append("..")
from utils.database import Database


class LatinCharError(Exception):
    """Raised when the input value is not a Latin Character"""
    pass


class Handler:

    def __init__(self, config):
        self._config = config

    def respond_to_dgc_request(self, dcc_request, db_entry):
        """ Beantwortet einen DCC-Antrag
            und ruft dazu handle_dgc_request auf"""
        payload = self.handle_dgc_request(dcc_request, db_entry)
        testId = dcc_request['testId']
        response = requests.post(   url=f'{self._config["dcc-endpoint"]}/version/v1/test/{testId}/dcc',
                                    cert=self._config["dcc-client-cert"],
                                    json=payload )
        logging.info(f'Upload encrypted data: TestID: {testId} Status Code: {response.status_code}')


    def handle_dgc_request(self, dcc_request, db_entry):
        "Erstellt eine Antwort auf einen DCC-Antrag"

        # Zufälligen Schlüssel erzeugen

        dek = random_bytes(32)

        # Payload erzeugen
        dcc_data = self._get_dgc_data(db_entry)
        dcc_data['t'][0]['ci'] = dcc_request['dcci']

        logging.debug(f'DCC-DATA = {dcc_data}')

        # Daten CBOR-kodieren
        cbor_data = self.dcc_cbor(dcc_data)
        # Konstante: COSE protected header (für den Hash)
        protected_header = cbor2.dumps({1:-7})
        # CBOR-Daten, die signiert werden: Protected header und Payload
        cbor_to_sign = cbor2.dumps(["Signature1",protected_header, b"", cbor_data] )
        # Es wird AES im CBC Modus mit einem IV von 16 0-Bytes verwendet
        cipher = Cipher(algorithms.AES(dek),modes.CBC( b'\x00'*16), default_backend() ).encryptor()
        # Die Daten werden nach PKCS7 gepaddet
        padder = padding.PKCS7(128).padder()
        padded_data = padder.update(cbor_data) + padder.finalize()
        # Die Daten mit Padding werden verschlüsselt
        encrypted_data = cipher.update(padded_data) + cipher.finalize()
        # Die Daten (ohne Padding) werden SHA-256 gehasht
        hasher = sha256()
        hasher.update( cbor_to_sign )
        hex_hash = hexlify(hasher.digest())
        # Der symmetrische Schlüssel wird mit dem Public Key verschlüselt
        encrypted_key = self._encrypt_dek_with_public_key(dek, dcc_request['publicKey'])

        return {
            "dataEncryptionKey": b64encode(encrypted_key).decode('utf-8'), # encrypted DEK as base64
            "encryptedDcc": b64encode(encrypted_data).decode('utf-8'), # encrypted DCC material as base64
            "dccHash": hex_hash.decode('utf-8') # DCC hash as hex
        }


    def dcc_cbor(self, certData, issuedAtTimestamp=None, expiredAfterSeconds=None ):
        if issuedAtTimestamp is None:
            issuedAtTimestamp = int(time())     # Wenn nichts angegeben, dann jetziger Zeitpunkt
        if expiredAfterSeconds is None:
            expiredAfterSeconds = 60 * 60 * 48  # Wenn nichts angegeben, 2 Tage Gültigkeit

        cborMap = {}

        sc = certData['t'][0]['sc']
        utc_dt = datetime.fromisoformat(sc[:-1])
        testedAtTimestamp = int((utc_dt - datetime(1970, 1, 1)).total_seconds())

        cborMap[4] = testedAtTimestamp + expiredAfterSeconds
        cborMap[6] = issuedAtTimestamp
        cborMap[1] = 'DE'
        cborMap[-260] = {1: certData}
        return cbor2.dumps(cborMap)


    def _encrypt_dek_with_public_key( self, dek, publicKeyStr):
        """Verschlüsselt den symmetrischen DEK mit dem publicKey
            dek: binär
            publicKeyStr: Base64 kodiertes DER
            """
        publicKey = load_pem_public_key( self._wrap_public_key(publicKeyStr) , default_backend() )
        encrypted_key = publicKey.encrypt(dek,
            asym_padding.OAEP(
                mgf=asym_padding.MGF1(algorithm=hashes.SHA256()),
                algorithm=hashes.SHA256(),
                label=None
            )
        )
        return encrypted_key


    def _wrap_public_key(self, key):
        "Base64 kodiertes DER + BEGIN/END-Markierungen ergibt PEM"
        return ('-----BEGIN PUBLIC KEY-----\n' + key + '\n-----END PUBLIC KEY-----').encode('utf-8')

    def _get_dgc_data(self, db_entry):
            
        data = self._config["dcc-template"].copy()

        #fn = family Name
        #gn = given Name
        #dob = date of birth
        #sc = registration date "Jahr-Monat-Tag"+"T"+"UTC-Zeit in HH:MM:SS"+"Z"
        #ma = RAT device ID
        #nm = RAT commercial name

        fn = db_entry[0]
        gn = db_entry[1]
        dob = db_entry[3]
        utc_dt = db_entry[2].astimezone(tz=timezone.utc).replace(tzinfo=None)
        sc = utc_dt.isoformat(timespec='seconds')+'Z'
        ma = f'{db_entry[4]}'


        data['nam']['fn'] = fn
        data['nam']['fnt'] = self._convertToICAONormal(fn)
        data['nam']['gn'] = gn
        data['nam']['gnt'] = self._convertToICAONormal(gn)
        data['dob'] = dob
        data['t'][0]['sc'] = sc
        data['t'][0]['ma'] = ma

        return data


    def _convertToICAONormal(self,string):
        string=string.upper()
        transl = {
            '1':'I',
            '2':'II',
            '3':'III',
            '4':'IV',
            '5':'V',
            '6':'VI',
            '7':'VII',
            '8':'VIII',
            '9':'IX',
            ' ':'<',
            '-':'<',
            '\'':'',
            ',':'',
            ':':'',
            ';':'',
            '.':'',
            'À':'A',
            'Á':'A',
            'Â':'A',
            'Ã':'A',
            'Ä':'AE',
            'Å':'AA',
            'Æ':'AE',
            'Ç':'C',
            'È':'E',
            'É':'E',
            'Ê':'E',
            'Ë':'E',
            'Ì':'I',
            'Í':'I',
            'Î':'I',
            'Ï':'I',
            'Ð':'D',
            'Ñ':'N',
            'Ò':'O',
            'Ó':'O',
            'Ô':'O',
            'Õ':'O',
            'Ö':'OE',
            'Ø':'OE',
            'Ù':'U',
            'Ú':'U',
            'Û':'U',
            'Ü':'UE',
            'Ý':'Y',
            'Þ':'TH',
            'Ā':'A',
            'Ă':'A',
            'Ą':'A',
            'Ć':'C',
            'Ĉ':'C',
            'Ċ':'C',
            'Č':'C',
            'Ď':'D',
            'Ð':'D',
            'Ē':'E',
            'Ĕ':'E',
            'Ė':'E',
            'Ę':'E',
            'Ě':'E',
            'Ĝ':'G',
            'Ğ':'G',
            'Ġ':'G',
            'Ģ':'G',
            'Ĥ':'H',
            'Ħ':'H',
            'Ĩ':'I',
            'Ī':'I',
            'Ĭ':'I',
            'Į':'I',
            'İ':'I',
            'I':'I',
            'Ĳ':'IJ',
            'Ĵ':'J',
            'Ķ':'K',
            'Ĺ':'L',
            'Ļ':'L',
            'Ľ':'L',
            'Ŀ':'L',
            'Ł':'L',
            'Ń':'N',
            'Ņ':'N',
            'Ň':'N',
            'Ŋ':'N',
            'Ō':'O',
            'Ŏ':'O',
            'Ő':'O',
            'Œ':'OE',
            'Ŕ':'R',
            'Ŗ':'R',
            'Ř':'R',
            'Ś':'S',
            'Ŝ':'S',
            'Ş':'S',
            'Š':'S',
            'Ţ':'T',
            'Ť':'T',
            'Ŧ':'T',
            'Ũ':'U',
            'Ū':'U',
            'Ŭ':'U',
            'Ů':'U',
            'Ű':'U',
            'Ų':'U',
            'Ŵ':'W',
            'Ŷ':'Y',
            'Ÿ':'Y',
            'Ź':'Z',
            'Ż':'Z',
            'Ž':'Z',
            'ẞ':'SS',
            'Ё':'E',
            'Ћ':'D',
            'Є':'IE',
            'Ѕ':'DZ',
            'І':'I ',
            'Ї':'I',
            'Ј':'J',
            'Љ':'LJ',
            'Њ':'NJ',
            'Ќ':'K',
            'ў':'U',
            'Џ':'DZ',
        }

        for s in string:
            if s in transl:
                string=string.replace(s,transl[s])              
            else:
                if s.isascii():
                    logging.debug(f'No conversion needed for character {s}')  
                else:
                    logging.error(f'Name contains non-latin character {s}.')
                    raise LatinCharError
        return string
    

    
def main(args):

    config = json.load( open(args.config_file, encoding='utf-8' ))
    response = requests.get( config["dcc-endpoint"]+'/version/v1/publicKey/search/'+config["lab-ID"] ,
                                cert=config["dcc-client-cert"])
    logging.info( f'Polling response status code: {response.status_code} Length: {len(response.text)}')
    DatabaseConnect = Database()
    handler = Handler(config)
    for dcc_request in response.json():
        logging.debug(dcc_request)
        sql = "Select Nachname, Vorname, Registrierungszeitpunkt, Geburtsdatum, Testtyp.Device_ID from Vorgang JOIN Testtyp ON Testtyp_id=Testtyp.id where Ergebnis =2 and CWA_request=1 and HashOfHash='%s' and Testtyp.Device_ID is not NULL;"%(dcc_request['testId'])
        content = DatabaseConnect.read_single(sql)
        logging.debug(content)
        if not content:
            logging.info('Either hash not found or test not permitted for DCC')
        else:
            try:
                handler.respond_to_dgc_request(dcc_request, content)
            except Exception as e:
                logging.error(e)


if __name__ == '__main__':
    logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
    logger = logging.getLogger('DCC Handler: %s' %(datetime.now()))
    logger.info('Starting DCC Request')
    parser = ArgumentParser(description='''Simulator for Covid-Test Center or Lab''')
    parser.add_argument('-f', '--config-file', default='config.json', help='Configuration file')
    #parser.add_argument( '--dry-run', action='store_true', help='Do not upload DCCs but write to dry_run.txt')
    args = parser.parse_args()
    main(args)

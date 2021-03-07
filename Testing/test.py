import sys
import logging
sys.path.append("..")
from utils.sendmail import send_test_mail

logging.basicConfig(level=logging.DEBUG,
                    format='%(asctime)s - %(name)s - %(levelname)s - %(message)s')
logger = logging.getLogger('Testing')
logger.debug('Starting')

send_test_mail()

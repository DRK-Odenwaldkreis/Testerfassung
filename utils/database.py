#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.


import sys
import time
sys.path.append("..")
from utils.readconfig import read_config
import os
import mysql.connector
import logging

logger = logging.getLogger('Database')
logger.debug('Logger for database was initialised')


class Disconnect(Exception):
    pass


class InsertError(Exception):
    pass


class UpdateError(Exception):
    pass

class DeleteError(Exception):
    pass

class QueryError(Exception):
    pass


class Database(object):

    # Constructor
    def __init__(self):
        try:
            logger.debug('Constructor was called')
            self.__host = read_config("MariaDB", "host")
            self.__user = read_config("MariaDB", "user")
            self.__socket = '/var/run/mysqld/mysqld.sock'
            self.__dbName = read_config("MariaDB", "db")
            self.__password = read_config("MariaDB", "password")
            self.connection = mysql.connector.connect(
                host=self.__host, user=self.__user, password=self.__password, db=self.__dbName)
            self.cursor = self.connection.cursor()
        except Exception as e:
            logger.error(
                'The following error occured in constructor of database: %s' % (e))
            raise Disconnect

#Insert,Update, read_all and read_single
    def insert(self, query, tupel):
        try:
            self.cursor.execute(query, tupel)
            self.connection.commit()
            return True
        except Exception as e:
            logger.error(
                'The following error occured in inserting: %s' % (e))
            self.connection.rollback()
            raise UpdateError
    
    def insert_feedbacked(self, query):
        try:
            self.cursor.execute(query)
            self.feedback = self.cursor.lastrowid
            self.connection.commit()
            return self.feedback
        except Exception as e:
            logger.error(
                'The following error occured in inserting: %s' % (e))
            self.connection.rollback()
            raise UpdateError

    def update(self, query):
        try:
            self.cursor.execute(query)
            self.connection.commit()
            return True
        except Exception as e:
            logger.error(
                'The following error occured in updating: %s' % (e))
            self.connection.rollback()
            raise UpdateError

    def read_all(self, query):
        try:
            self.cursor.execute(query)
            self.result = self.cursor.fetchall()
            if self.result is not None:
                return self.result
        except Exception as e:
            logger.error(
                'The following error occured in read all: %s' % (e))
            raise QueryError

    def read_single(self, query):
        try:
            self.cursor.execute(query)
            self.result = self.cursor.fetchone()
            if self.result is not None:
                return self.result
        except Exception as e:
            logger.error(
                'The following error occured in read all: %s' % (e))
            raise QueryError
    
    def delete(self, query):
        try:
            self.cursor.execute(query)
            self.connection.commit()
            return True
        except Exception as e:
            logger.error(
                'The following error occured in deleting: %s' % (e))
            self.connection.rollback()
            raise DeleteError


    def close_connection(self):
        try:
            logger.debug("Closing Cursor and connection")
            self.cursor.close()
            self.connection.close()
            logger.debug('Connection and Cursor closed')
        except Exception as e:
            logger.error('The following error occured: %s' % (e))
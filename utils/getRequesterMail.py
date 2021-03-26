#!/usr/bin/python3
# coding=utf-8

# This file is part of DRK Testzentrum.


import os
import sys
sys.path.append("..")
from utils.database import Database

def get_Mail_from_UserID(id):
    try:
        DatabaseConnect = Database()
        sql = 'Select email from li_user where id = %s' % (id)
        userMail = DatabaseConnect.read_single(sql)
        return userMail[0]
    except:
        return "info@testzentrum-odenwald.de"


def get_Mail_List(idList):
    try:
        DatabaseConnect = Database()
        sql = "Select username from li_user where id in %s" % (str(tuple(idList)))
        userMail = DatabaseConnect.read_all(sql)
        mailingList = []
        for i in userMail:
            mailingList.append(i[0])
        return mailingList
    except Exception as e:
        print("The following error occured in reminder job: %s" % (e))


def get_Mail_from_StationID(id):
    try:
        DatabaseConnect = Database()
        sql = 'Select email from li_user where Station = %s and email is not NULL' % (id)
        userMail = DatabaseConnect.read_single(sql)
        if len(userMail) == 0:
            userMail = ["info@testzentrum-odenwald.de"]
        return userMail
    except:
        return ["info@testzentrum-odenwald.de"]


def get_Leitung_from_StationID(id):
    try:
        DatabaseConnect = Database()
        sql = 'Select email from li_user where Station = %s and email is not NULL and role_5 = 1' % (id)
        userMail = DatabaseConnect.read_single(sql)
        if len(userMail) == 0:
            userMail = ["info@testzentrum-odenwald.de"]
        return userMail
    except:
        return ["info@testzentrum-odenwald.de"]

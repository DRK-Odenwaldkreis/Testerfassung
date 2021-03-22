#!/usr/bin/env python
# -*- coding: utf-8 -*-

# This file is part of DRK Testerfassung.


import sys
import pyqrcode
import random
import png
from fpdf import FPDF
import time
import os
import os.path
import datetime
sys.path.append("..")
from utils.slot import get_slot_time


FreeSans = '../utils/Schriftart/FreeSans.ttf'
FreeSansBold = '../utils/Schriftart/FreeSansBold.ttf'
Logo = '../utils/logo.png'


class PDFgenerator(FPDF):

	
	def create_page(self):
		self.add_page()
		self.add_font('GNU', '', FreeSans, uni=True)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 30)
		self.cell(10, 30, '', ln=1)
		self.cell(200, 5, 'Test-Ticket für einen', ln=1, align='C')
		self.cell(200, 30, 'SARS-CoV-2-Schnelltest(PoC)', ln=1, align='C')
		self.set_font('GNU', '', 20)
		self.cell(200,15, 'Name: ' + self.nachname + ', ' + self.vorname, ln=1)
		self.cell(200,15, 'Datum: ' + self.date.strftime("%d.%m.%Y"), ln=1)
		self.cell(200,15, 'Uhrzeit: ' + str(self.appointment), ln=1)
		self.cell(20, 30, '', ln=1)
		self.qrcode = pyqrcode.create(str(self.code), error='L')
		self.qrcode.png('tmp/'+str(self.code) + '.png', scale=5,quiet_zone=2)
		self.image('tmp/'+ str(self.code) + '.png', x=85)
		self.cell(20, 0, '', ln=1)
		os.remove('tmp/'+str(self.code) + '.png')

	def creatPDF(self,content):
		self.code = content[6]
		self.slot = content[3]
		self.stunde = content[4]
		self.vorname = content[0]
		self.nachname = content[1]
		self.date = content[5]
		self.appointment = get_slot_time(self.slot, self.stunde)
		self.time = datetime.date.today().strftime("%d.%m.%Y")
		self.create_page()
		self.filename = "../../Tickets/" + str(self.vorname) + "-" + str(self.nachname) + "_" + str(self.date) + ".pdf"
		self.output(self.filename)
		return self.filename
	

	def header(self):
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)
		self.image(Logo, x=7, y=10, w=100, h=24, type='PNG')
		self.ln(10)
		self.image(Logo, x=7, y=10, w=100, h=24, type='PNG')


	def footer(self):
		pass


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


FreeSans = '../utils/Schriftart/FreeSans.ttf'
FreeSansBold = '../utils/Schriftart/FreeSansBold.ttf'
Logo = '../utils/logo.png'


class PDFgenerator(FPDF):

	
	def create_page(self):
		self.add_page()
		self.add_font('GNU', '', FreeSans, uni=True)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 30)
		self.cell(10, 25, '', ln=1)
		self.cell(200, 10, 'Login für', ln=1, align='C')
		self.cell(200, 25, self.station, ln=1, align='C')
		self.set_font('GNU', '', 20)
		self.string = "/user/" + str(self.user) + "/password/" + str(self.password)
		self.qrcode = pyqrcode.create(str(self.string), error='Q')
		self.qrcode.png('tmp/'+str(self.user) + '.png', scale=6, quiet_zone=4)
		self.image('tmp/'+str(self.user) + '.png', x=80)
		#self.cell(10, 0, '', ln=1)
		self.current_x=self.get_x
		self.cell(200, 10, str(self.string), ln=1, align='C')
		os.remove('tmp/'+str(self.user) + '.png')


	def creatPDF(self, user, password, station):
		self.whitelist = set('abcdefghijklmnopqrstuvwxyz ABCDEFGHIJKLMNOPQRSTUVWXYZ')
		self.user = ''.join(filter(self.whitelist.__contains__, user)) 
		self.password = password
		self.station = station
		self.time = datetime.date.today().strftime("%d.%m.%Y")
		self.create_page()
		self.filename = "../../LoginSheet/Login_" + str(self.user) + ".pdf"
		self.output(self.filename)
		return self.filename
	

	def header(self):
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)
		self.image(Logo, x=7, y=10, w=100, h=24, type='PNG')
		self.ln(10)



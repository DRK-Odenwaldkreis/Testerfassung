#!/usr/bin/env python
# -*- coding: utf-8 -*-

# This file is part of DRK Testerfassung.


import sys
import pyqrcode
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

	
	def create_page(self,code):
		self.add_page()
		self.code = code
		self.add_font('GNU', '', FreeSans, uni=True)
		self.add_font('GNU', 'B', FreeSansBold, uni=True)
		self.set_font('GNU', 'B', 30)
		self.cell(10, 30, '', ln=1)
		self.cell(200, 40, 'Testkarte', ln=1, align='C')
		self.set_font('GNU', '', 20)
		self.qrcode = pyqrcode.create(self.code, error='L')
		self.qrcode.png('tmp/code.png', scale=12)
		self.image("tmp/code.png", x=50)
		self.cell(10, 40, '', ln=1)
		self.cell(200, 10, 'Kartennummer: %s' % (self.code), ln=1, align='C')

	def add_codes(self,codes):
		self.codes = codes

	def creatPDF(self):
		self.time = datetime.date.today().strftime("%d.%m.%Y")
		for i in self.codes:
			self.create_page(i)
		self.filename = "../../Reports/Laufkarten.pdf"
		self.output(self.filename)
		return self.filename
	

	def header(self):
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)
		self.image(Logo, x=7, y=10, w=100, h=24, type='PNG')
		self.ln(10)


	def footer(self):
		self.set_y(-15)
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)


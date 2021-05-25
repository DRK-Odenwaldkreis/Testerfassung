#!/usr/bin/env python
# -*- coding: utf-8 -*-

# This file is part of DRK Testzentrum.

import matplotlib.pyplot as plt
import sys
import numpy as np
from fpdf import FPDF
import time
import os
import os.path
import datetime
import matplotlib.gridspec as gridspec
sys.path.append("..")

Logo = '../utils/logo.png'

FreeSans = '../utils/Schriftart/FreeSans.ttf'
FreeSansBold = '../utils/Schriftart/FreeSansBold.ttf'


class MyPDF(FPDF):

	time='zeit'

	def header(self):
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)
		self.cell(40, 10, 'Testzentrum Odenwaldkreis:', ln=1)
		self.image(Logo, x=7, y=10, w=100, h=24, type='PNG')
		self.ln(20)


	def footer(self):
		self.set_y(-15)
		self.add_font('GNU', '', FreeSans, uni=True)
		self.set_font('GNU', '', 11)

		page= 'Seite %s/ {nb}' % self.page_no()

		self.cell(0, 10, page, align='R')


class PDFgenerator:

	def __init__(self, content, date):
		self.content=content
		self.date=date
		self.totalSeconds=0
		self.stationID = self.content[0][0]
		self.station = self.content[0][1]
		self.address = self.content[0][2]
		self.tests = self.content[1]
		self.positiv = self.content[2]
		self.negativ = self.content[3]
		self.unklar = self.content[4]
		self.cycleTime = self.content[5]
		self.gebDates = self.content[6]
		self.numberChildren = self.content[7]
		self.CWA_anonym = self.content[8]
		self.CWA_named = self.content[9]
		self.pre_reg = self.content[10]
		self.poc_reg = self.content[11]
		self.regHours = np.array(self.content[12])
		self.regMinutes = np.array(self.content[13])

		# Pie chart, where the slices will be ordered and plotted counter-clockwise:
		self.labels = 'Positiv', 'Negativ', 'Unklar'
		self.sizes = [self.positiv, self.negativ, self.unklar]
		self.explode = (1, 0.1, 0)  # only "explode" the 2nd slice (i.e. 'Hogs')
		self.fig, self.ax = plt.subplots(4,2)
		plt.subplots_adjust(wspace=0.4,hspace=1.5,left=0.11,top=0.95, bottom=0.1)
		"""self.fig.suptitle("Gesamtanzahl der Tests: %s" % (self.tests))
		self.ax[0,0].pie(self.sizes, explode=self.explode, labeldistance=2.0,pctdistance=3.5,labels=self.labels, autopct=lambda p: '{:.2f}%  ({:,.0f})'.format(p, p * sum(self.sizes)/100),
                    shadow=False, startangle=90)"""
		self.labels = ['Positiv', 'Negativ','Unklar']
		self.sizes = np.array([self.positiv, self.negativ,self.unklar])
		self.ax[0,0].barh(self.labels, self.sizes)
		self.ax[0,0].set_title("Gesamtanzahl der Tests: %s" % (self.tests))
		self.ax[0,0].set_xlabel("Anzahl")
		self.ax[0,0].axis(xmin=0,xmax=np.max(self.sizes)*1.7)
		for i, v in enumerate(self.sizes):
			self.ax[0,0].text(v, i, f'{v} ({round(v/(self.tests)*100,1)}%)', color='black', va='center')
		

		# Equal aspect ratio ensures that pie is drawn as a circle.
		#self.ax[0,0].axis('equal')
		# Histogram of Durchlaufzeiten
		self.cycleTimeArray = np.array(self.cycleTime)
		self.ax[0,1].hist(self.cycleTimeArray, range(15,30),color = "green")
		self.ax[0,1].set_title("Schnitt: %s min" %(int(self.cycleTimeArray.mean())))
		self.ax[0,1].set_ylabel("Anzahl")
		self.ax[0,1].set_xlabel("Durchlaufzeit [min]")
		#Pi charts kids <-> Adults
		self.ageArray = np.array(self.gebDates)
		self.ax[1,0].hist(self.ageArray, 10, color = "green")
		self.ax[1,0].set_title("Altersschnitt: %s Jahre" %(int(self.ageArray.mean())))
		self.ax[1,0].set_xlabel("Alter")
		self.ax[1,0].set_ylabel("Anzahl")
		#Bar charts kids <-> Adults
		self.labels = ['Kinder', 'Erwachsene']
		self.sizes = np.array([self.numberChildren, self.tests-self.numberChildren])
		self.bar = self.ax[1,1].bar(self.labels, self.sizes)
		self.ax[1,1].set_title("Kinder/Erwachsense")
		self.ax[1,1].set_ylabel("Anzahl")
		self.ax[1,1].axis(ymin=0,ymax=np.max(self.sizes)*1.5)
		for bar in self.bar:
			height = bar.get_height()
			label_x_pos = bar.get_x() + bar.get_width() / 2
			self.ax[1,1].text(label_x_pos, height, s=f'{height} ({round(height/(self.tests)*100,1)}%)', ha='center',va='bottom')
		#Bar charts CWA Tests
		self.labels = ['Anonym', 'Personalisiert']
		self.sizes = np.array([self.CWA_anonym,self.CWA_named])
		self.bar = self.ax[2,0].bar(self.labels, self.sizes)
		self.ax[2,0].set_title(f'Corona Warn ({round((self.CWA_anonym+self.CWA_named)*100/(self.tests),1)}%)')
		self.ax[2,0].set_ylabel("Anzahl")
		self.ax[2,0].axis(ymin=0,ymax=np.max(self.sizes)*1.5)
		for bar in self.bar:
			height = bar.get_height()
			label_x_pos = bar.get_x() + bar.get_width() / 2
			self.ax[2,0].text(label_x_pos, height, s=f'{height} ({round(height/(self.CWA_anonym+self.CWA_named)*100,1)}%)', ha='center',va='bottom')
		#Pi charts Preregistered <-> Vor Ort
		self.labels = ['Selbstregistriert', 'Vor Ort']
		self.sizes = np.array([self.pre_reg,self.poc_reg])
		self.bar = self.ax[2,1].bar(self.labels, self.sizes)
		self.ax[2,1].set_title("Registrierungsverhalten")
		self.ax[2,1].set_ylabel("Anzahl")
		self.ax[2,1].axis(ymin=0,ymax=np.max(self.sizes)*1.5)
		for bar in self.bar:
			height = bar.get_height()
			label_x_pos = bar.get_x() + bar.get_width() / 2
			self.ax[2,1].text(label_x_pos, height, s=f'{height} ({round(height/(self.pre_reg+self.poc_reg)*100,1)}%)', ha='center',va='bottom')
		
		
		self.heatmap, xedges,yedges, self.img = plt.hist2d(self.regHours, self.regMinutes, bins=(14, 4), cmap=plt.cm.BuPu, range=((6, 20), (0, 60)))
		self.ax[3,0].set_title("Auslastung")
		self.ax[3,0].set_ylabel("Minute")
		self.ax[3,0].set_xlabel("Stunde")
		self.ax[3,0].set_xticks(np.array([6,8,10,12,14,16,18,20]))
		self.ax[3,0].set_yticks(np.array([0,30,60]))
		self.im = self.ax[3,0].imshow(self.heatmap.T,origin='lower', cmap=plt.cm.BuPu, interpolation="none",extent=[6,20,0,60],aspect="auto")
		self.fig.colorbar(self.im, ax=self.ax[3,0])
		
		self.ax[3,1].set_visible(False)

		"""
		self.labels = ['Selbstregistriert', 'Vor Ort']
		self.sizes = np.array([self.pre_reg,self.poc_reg])
		self.bar = self.ax[2,1].bar(self.labels, self.sizes)
		self.ax[2,1].set_title("Registrierungsverhalten")
		self.ax[2,1].set_ylabel("Anzahl")
		self.ax[2,1].axis(ymin=0,ymax=np.max(self.sizes)*1.5)
		for bar in self.bar:
			height = bar.get_height()
			label_x_pos = bar.get_x() + bar.get_width() / 2
			self.ax[2,1].text(label_x_pos, height, s=f'{height} ({round(height/(self.pre_reg+self.poc_reg)*100,1)}%)', ha='center',va='bottom')

		"""
		plt.savefig('tmp/' + str(self.date) + '.png', dpi=(200))

	def generate(self):

		pdf=MyPDF()
		#pdf.time=self.date
		# pdf.name=self.name
		pdf.alias_nb_pages()
		pdf.add_page()
		pdf.set_auto_page_break(True, 25)
		pdf.add_font('GNU', '', FreeSans, uni=True)
		pdf.add_font('GNU', 'B', FreeSansBold, uni=True)

		pdf.set_font('GNU', 'B', 14)

		pdf.cell(20, 10, 'Tagesprotokoll für %s' % (self.date), ln=1)

		pdf.set_font('GNU', '', 14)

		pdf.cell(20, 10, 'Erstellt: {}'.format(datetime.datetime.now().strftime("%Y-%m-%d um %H:%M:%S"), ln=1))
		pdf.set_font('GNU', 'B' , 20)
		pdf.ln(15)
		pdf.set_font('GNU', 'B', 14)
		pdf.cell(35, 10, 'Testzentrum: %s, %s' %(self.station,self.address), 0, 1)

		current_x =pdf.get_x()
		current_y =pdf.get_y()

		pdf.line(current_x, current_y, current_x+190, current_y)
		pdf.ln(20)
		pdf.image('tmp/' + str(self.date) + '.png', w=210, h=160)
		os.remove('tmp/'+str(self.date) + '.png')
		pdf.set_font('GNU', '', 14)
		self.filename = "../../Reports/Tagesreport_Testzentrum_ID_" + str(self.stationID) + "_"+str(self.date) + ".pdf"
		pdf.output(self.filename)
		return self.filename

aux=FPDF('P', 'mm', 'A4')

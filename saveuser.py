#!/usr/bin/env python

import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
import mysql.connector
from mysql.connector import Error
from mysql.connector import errorcode
import Adafruit_CharLCD as LCD
import picamera
# connect to mysql database
db = mysql.connector.connect(
  host="localhost",
  user="attendanceadmin",
  passwd="duyho",
  database="attendancesystem"
)

# convert image into binary data
def convertToBinaryData(filename):
    #Convert digital data to binary format
  try:
    with open(filename, 'rb') as file:
        binaryData = file.read()
    return binaryData
  except mysql.connector.Error as error :
    connection.rollback()
    print("Failed convert binary data".format(error))

# insert BLOB

# fetch cursor
cursor = db.cursor()
reader = SimpleMFRC522()
lcd = LCD.Adafruit_CharLCD(4, 24, 23, 17, 18, 22, 16, 2, 4);
# set up GPIO
GPIO.setmode(GPIO.BCM)
led_signal = 5
buzzer_signal = 12
GPIO.setup(led_signal, GPIO.OUT)
GPIO.setup(buzzer_signal,GPIO.OUT)
# class buzzer and signal led
class Signal:
  'class to make a signal'
  def __init__(self,led,buzzer):
      self.led = led
      self.buzzer =buzzer
  def blink_led(self):
      GPIO.output(self.led,GPIO.HIGH)
      time.sleep (0.5)
      GPIO.output(self.led,GPIO.LOW)
  def turnon_led(self):
      GPIO.output(self.led,GPIO.HIGH)
  def turnoff_led(self):
      GPIO_output(self.led,GPIO.LOW)
  def buzzer_ring(self):
      GPIO.output(self.buzzer,GPIO.HIGH)
      time.sleep (1)
      GPIO.output(self.buzzer,GPIO.LOW)
indication = Signal(led_signal,buzzer_signal)
try:
  while True:
    lcd.clear()
    lcd.message('Dat the vao\nDang ky')
    id, text = reader.read()
    print(id)
    print(text)
    #searching user table to see if rows have a matching RFID UID to the ID we retrieved from RFID card
    cursor.execute("SELECT id FROM users WHERE rfid_uid="+str(id))
    #Grab data we retrieved. This function will grab one row from the returned results
    cursor.fetchone()

    if cursor.rowcount >= 1:
      lcd.clear()
      lcd.message("Ghi de\nuser ton tai?")
      # Insert y/n from keyboard
      overwrite = input("Overwite (Y/N)? ")
      # Create signal
      indication.blink_led()
      indication.buzzer_ring()
      if overwrite[0] == 'Y' or overwrite[0] == 'y':
        lcd.clear()
        lcd.message("User Ghi de.")
        time.sleep(1)
        # create a querry to update fields: name, rfid_uid
        new_name = input("Insert Name: ")
        new_mssv = input("Insert MSSV: ")

        sql_insert = "UPDATE users SET name = %s, MSSV = %s  WHERE rfid_uid=%s"
        cursor.execute(sql_insert,(new_name,new_mssv,id))
        db.commit()
        #sql_insert_ = "UPDATE users SET %s=now()  WHERE rfid_uid=%s"
        #cursor.execute(sql_insert_,("created",id))
        #db.commit()
      else:
        continue;

    else:
      #create a querry to insert new name and rfid_uid
      new_name = input("Insert Name: ")
      new_mssv = input("Insert MSSV: ")
      sql_insert = "INSERT INTO users (name,MSSV ,rfid_uid) VALUES (%s,%s, %s)"
      cursor.execute(sql_insert,(new_name,new_mssv,id))
    lcd.clear()
    lcd.message('Nhap ten moi')
    # insert new name
    #create signal
    indication.blink_led()
    indication.buzzer_ring()
    #execute the querry we formed it before, which insert name and id
    db.commit()
    print("Insert DB success ! ")
    # capture image from camera
    camera = picamera.PiCamera()
    camera.resolution = (800,600)
    camera.start_preview()
    time.sleep(5)
    camera.awb_mode = 'fluorescent'
    camera.capture('/home/pi/snapshot1.jpeg',resize=(200,200))
    camera.stop_preview()
    # insert image into database
    
    try:
      sql_insert_img = "UPDATE users SET photo = %s  WHERE rfid_uid=%s"
      empPicture = convertToBinaryData("/home/pi/snapshot1.jpeg")
      cursor.execute(sql_insert_img, ( empPicture,id ))
      #commit all the change in sql
      db.commit()
    except mysql.connector.Error as error :
      connection.rollback()
      print("Failed insert binary data".format(error))
    #insertBLOB(id,new_name,'/hom/pi/attendancesystem/1200px-Logo-hcmut.svg.png')
    lcd.clear()
    lcd.message("User " + new_name + "\nSaved")
    time.sleep(2)
except:
	print("Insert DB FAILED")

finally:
  GPIO.cleanup()
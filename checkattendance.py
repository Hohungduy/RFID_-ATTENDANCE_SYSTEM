#!/usr/bin/env python
import time
import RPi.GPIO as GPIO
from mfrc522 import SimpleMFRC522
import mysql.connector
from mysql.connector import Error
from mysql.connector import errorcode
import Adafruit_CharLCD as LCD

db = mysql.connector.connect(
  host="localhost",
  user="attendanceadmin",
  passwd="duyho",
  database="attendancesystem"
)

cursor = db.cursor()
reader = SimpleMFRC522()

lcd = LCD.Adafruit_CharLCD(4, 24, 23, 17, 18, 22, 16, 2, 4);
# convert image into binary data
def convertToBinaryData(filename):
    #Convert digital data to binary format
    with open(filename, 'rb') as file:
        binaryData = file.read()
    return binaryData

# insert BLOB
def insertBLOB(emp_id, name, photo, biodataFile):
    print("Inserting BLOB into users table")
    try:
        connection = mysql.connector.connect(host='localhost',
                             database='attendancesystem',
                             user='attendanceadmin',
                             password='duyho')
        cursor = connection.cursor(prepared=True)
        sql_insert_blob_query = """ INSERT INTO users
                          (id, name, phot) VALUES (%s,%s,%s,%s)"""
        empPicture = convertToBinaryData(photo)
        file = convertToBinaryData(biodataFile)
        # Convert data into tuple format
        insert_blob_tuple = (emp_id, name, empPicture, file)
        result  = cursor.execute(sql_insert_blob_query, insert_blob_tuple)
        connection.commit()
        print ("Image and file inserted successfully as a BLOB into python_employee table", result)
    except mysql.connector.Error as error :
        connection.rollback()
        print("Failed inserting BLOB data into MySQL table {}".format(error))
    finally:
        #closing database connection.
        if(connection.is_connected()):
            cursor.close()
            connection.close()
            print("MySQL connection is closed")
# set up GPIO
GPIO.setmode(GPIO.BCM)
led_signal = 5
buzzer_signal = 12
GPIO.setup(led_signal, GPIO.OUT)
GPIO.setup(buzzer_signal,GPIO.OUT)
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
    lcd.message('Dat the vao\nrGhi danh co mat')
    id, text = reader.read()

    cursor.execute("Select id, name FROM users WHERE rfid_uid="+str(id))
    result = cursor.fetchone()

    lcd.clear()

    if cursor.rowcount >= 1:
      lcd.message("Chao mung " + result[1])
      indication.blink_led()
      indication.buzzer_ring()
      cursor.execute("INSERT INTO attendance (user_id) VALUES (%s)", (result[0],) )
      db.commit()
    else:
      lcd.message("User khong ton tai.")
      indication.blink_led()
      indication.buzzer_ring()
    time.sleep(2)
finally:
  GPIO.cleanup()


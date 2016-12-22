# ANT - Cadence, Speed Sensor AND Heart Rate Monitor - Example
#
# Copyright (c) 2012, Gustav Tiger <gustav@tiger.name>
#
# Permission is hereby granted, free of charge, to any person obtaining a
# copy of this software and associated documentation files (the "Software"),
# to deal in the Software without restriction, including without limitation
# the rights to use, copy, modify, merge, publish, distribute, sublicense,
# and/or sell copies of the Software, and to permit persons to whom the
# Software is furnished to do so, subject to the following conditions:
#
# The above copyright notice and this permission notice shall be included in
# all copies or substantial portions of the Software.
#
# THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
# IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
# FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
# AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
# LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
# FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
# DEALINGS IN THE SOFTWARE.

from __future__ import absolute_import, print_function

from ant.easy.node import Node
from ant.easy.channel import Channel
from ant.base.message import Message

import logging
import struct
import threading
import sys
import time
import math
from pymongo import MongoClient

NETWORK_KEY= [0xb9, 0xa5, 0x21, 0xfb, 0xbd, 0x72, 0xc3, 0x45]

class Monitor():
    def __init__(self):
        self.crank_revs = 0
        self.speed = "n/a"
        self.cadence = 0
        self.session_length = 0
        self.last_data_time = 0
        #time reported by sensor (in 1/1024ths of a second)
        self.device_time = 0
        self.session_start_time = 0

        #when we last got an update from the device
        self.last_device_time = 0

    def on_data_speed(self, data):
        self.speed = str(data[7]*256 + data[6])
        self.display()

    def on_data_cadence(self, data):
        old_crank_revs = self.crank_revs
        old_device_time = self.device_time
        current_time = time.time()
      

        #parse data from device
        self.crank_revs = data[7]*256 + data[6]
        self.device_time = data[5]*256 + data[4]

        revdiff = self.crank_revs - old_crank_revs
        timediff = self.device_time - old_device_time

        #try to calculate the cadence
        if (timediff == 0):
            pass
        else:
            self.cadence = (60 * 1024 / timediff) * revdiff

        

        #if it's been more than 2 seconds since we heard from the device, new session
        if self.last_data_time + 2 < current_time:
            self.new_session()
        else:
            #has the device time changed?
            if self.device_time != old_device_time:
                self.update_session()
                self.last_device_time = current_time
            else:
                #how long has it been?
                if self.last_device_time + 2 > current_time:
                    self.update_session()
                else:
                    self.new_session()
                    
        #update our saved times
        self.last_data_time = current_time
       
        
        self.display()

    def new_session(self):
        self.session_length = 0
        self.session_start_time = time.time()

        #it's a new session so start a new record 
        result = self.db.sessions.insert_one(
            {
                "start_time" : self.session_start_time,
                "merits_earned" : self.session_length,
                "last_updated" : self.session_start_time,
                "cadence"   : self.cadence
            }
        )

        #save the session Id for now
        self.session_id = result

    def update_session(self):
        current_time = time.time()

        self.session_length = current_time - self.session_start_time
        
        #update our mongo record
        self.db.sessions.update_one(
            {
                '_id' : self.session_id.inserted_id
            },
            {
                "$set" : {
                    "merits_earned" : self.session_length,
                    "last_updated" : current_time,
                    "cadence"   : self.cadence
                }
            }


        )

    def display(self):
        merits = math.floor(self.session_length)
        string = " Pedal revolutions: " + str(self.crank_revs) + " Cadence: " + str(self.cadence)  + " Merits this session: " + str(merits)

        sys.stdout.write(string)
        sys.stdout.flush()
        sys.stdout.write("\b" * len(string))


def main():
    # logging.basicConfig()

    mongo = MongoClient()

    monitor = Monitor()
    monitor.db = mongo.merits


    node = Node()
    node.set_network_key(0x00, NETWORK_KEY)

    channel = node.new_channel(Channel.Type.BIDIRECTIONAL_RECEIVE)

    channel.on_broadcast_data = monitor.on_data_speed
    channel.on_burst_data = monitor.on_data_speed

    channel.set_period(8188)
    channel.set_search_timeout(255)
    channel.set_rf_freq(57)
    channel.set_id(0, 123, 0)

    channel_cadence_speed = node.new_channel(Channel.Type.BIDIRECTIONAL_RECEIVE)

    channel_cadence_speed.on_broadcast_data = monitor.on_data_cadence
    channel_cadence_speed.on_burst_data = monitor.on_data_cadence

    channel_cadence_speed.set_period(8102)
    channel_cadence_speed.set_search_timeout(255)
    channel_cadence_speed.set_rf_freq(57)
    channel_cadence_speed.set_id(0, 122, 0)

    try:
        channel.open()
        channel_cadence_speed.open()
        node.start()
        print("after start")
    finally:
        node.stop()
    
if __name__ == "__main__":
    main()


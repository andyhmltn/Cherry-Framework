#! /usr/bin/python
import urllib, urllib2, os.path
import sys, time, re, platform
import getpass, hashlib, pickle

print "**User Class Framework Command line - v0.1a**"
def colourText(x, color):
	colors = {'error':'31', 'warning':'33', 'alert':'35'}
	if platform.system() == 'Windows':
		return x
	else:
		return "\033[" + colors[color] + "m" + x + "\033[0m"
def setConfig():
	config = str(raw_input(">>(Enter the site URL now) "))	
	_list = [config]
	pickle.dump(_list,open( "cmd/config","wb"))

try:
	open('cmd/config')
except IOError as e:
	print colourText("-----------------\n\
**WARNING!*** The config file hasn\'t been created. Please type the LIVE url of your site you wish to interact with.\
IE: If you\'re on localhost you would type \"localhost\" with no http://. If you are at www.bananas.com you would type that.\
This command line will then interact with that site from then on. From there you can manage the framework.\
To edit the config file later just go to config/cmd.config\n\
-----------------", 'warning')
	setConfig()
	url_base = pickle.load(open("cmd/config", "rb"))
	url = 'http://' + url_base[0] + '/cmd/response.php'
	values = {'connection' : 'established'}
	response = urllib2.urlopen(urllib2.Request(url, urllib.urlencode(values)))

if len(sys.argv) > 1:
	key_unhashed = sys.argv[1]
	key = hashlib.sha256('4224' + sys.argv[1] + '4224').hexdigest()
else:
	print colourText("Warning: You didn\'t define a key. To define one restart the script with: python script.py keyhere", 'error')
	key_unhashed = 'notdefined'
	key = 'notdefined'

if os.path.isfile('cmd/config'):
	url_base = pickle.load(open("cmd/config", "rb"))
	url = 'http://' + url_base[0] + '/cmd/response.php'
	values = {'connection' : 'established'}
	response = urllib2.urlopen(urllib2.Request(url, urllib.urlencode(values)))
def thread_1():
	command()
		
def genPass(x):
	return hashlib.sha256(x).hexdigest()

def sendCommand(command, _key, os):
	values = {'command' : command, 'key' : _key, 'os' : os}
	response = urllib2.urlopen(urllib2.Request(url, urllib.urlencode(values)))
	return response.read()

def command():
	currentSystem = platform.system()
	command = str(raw_input(">> "))
	if command == 'exit':
		exit()
	elif command == 'key':
		print key_unhashed
	elif command == 'set url':
		setConfig()
		print "Configuration file changed. Please restart the command line (the CL will exit in 3 seconds)"
		time.sleep(3)
		exit()
	elif command == 'read config file':
		config = open('cmd/config', 'r')
		print config.read()
	else:
		response = sendCommand(command, key, currentSystem)
		print response
	thread_1()

command()
#!/usr/bin/python
import signal, sys
import json
import requests

from evdev import InputDevice, categorize, ecodes

#This will allow you to work the grocy inventory with a barcode scanner
UPC_DATABASE_API='YOUR_TOKEN'
buycott_token='YOUR_TOKEN'
GROCY_API='YOUR_TOKEN'
add_id='YOUR_ADD_BARCODE'
base_url = 'YOUR_BASE_URL'
ADD = 0
barcode = ''
found = ''
device = InputDevice('/dev/input/event0') # Replace with your device
scancodes = {
	11:	u'0',
	2:	u'1',
	3:	u'2',
	4:	u'3',
	5:	u'4',
	6:	u'5',
	7:	u'6',
	8:	u'7',
	9:	u'8',
	10:	u'9'
}
NOT_RECOGNIZED_KEY = u'X'

def increase_inventory(upc):
    #Need to lookup our product_id, if we don't find one we'll do a search and add it
    product_id_lookup(upc)
    print ("Increasing %s") % (product_name)
    url = base_url+"/stock/products/%s/add" % (product_id)
    data = {'amount': purchase_amount,
        'transaction_type': 'purchase'}
    #We have everything we need now in order to complete the rest of this function
    grocy_api_call_post(url, data)
    #As long as we get a 200 back from the app it means that everything went off without a hitch
    if response_code != 200:
        print ("Increasing the value of %s failed") % product_name
    barcode = ''

def decrease_inventory(upc):
    #Going to see if we can find a product_id, if we don't find it we'll add it.  Problem here though is that we need to have a quantity in the system.  If there isn't any we'll error because there is nothing to decrease.  This is ok
    product_id_lookup(upc)
    print("Stepping into the decrease")
    #Lets make sure we can actually decrease this before we get too crazy
    if stock_amount > 0:
        print ("Decreasing %s by 1") % (product_name)
        url = base_url+"/stock/products/%s/consume" % (product_id)
        data = {'amount': 1,
                'transaction_type': 'consume',
                'spoiled': 'false'}
        #We now have everything we need and we can now proceed
        grocy_api_call_post(url, data)
        if response_code == 400:
            print ("Decreasing the value of %s failed, are you sure that there was something for us to decrease?") % product_name
    else:
        print ("The current stock amount is 0 so there was nothing for us to do here")
    barcode=''

def product_id_lookup(upc):
    #Need to declare this as a global and we'll do this again with a few others because we need them elsewhere
    global product_id
    print("Looking up the product_id")
    #Lets check to see if the UPC exists in grocy
    url = base_url+"/stock/products/by-barcode/%s" % (upc)
    headers = {
        'cache-control': "no-cache",
        'GROCY-API-KEY': GROCY_API
    }
    r = requests.get(url, headers=headers)
    r.status_code
    #Going to check and make sure that we found a product to use.  If we didn't find it lets search the internets and see if we can find it.
    if r.status_code == 400:
        upc_lookup(upc)
    else:
        j = r.json()
        global product_id
        product_id = j['product']['id']
        global purchase_amount
        purchase_amount = j['product']['qu_factor_purchase_to_stock']
        global product_name
        product_name = j['product']['name']
        print ("Our product_id is %s") % (product_id)
        global stock_amount
        stock_amount = j['stock_amount']

def upc_lookup(upc):
    if UPC_DATABASE_API != '':
        print("Looking up the UPC")
        url = "https://api.upcdatabase.org/product/%s/%s" % (upc, UPC_DATABASE_API)
        headers = {
            'cache-control': "no-cache",
        }
        try:
            r = requests.get(url, headers=headers)
            if r.status_code==200:
                print("UPC DB found it so now going to add it to the system")
                j = r.json()
                name = j['title']
                description = j['description']
                #We now have what we need to add it to grocy so lets do that
                add_to_system(upc, name, description)
        except requests.exceptions.Timeout:
            print("The connection timed out")
        except requests.exceptions.TooManyRedirects:
            print ("Too many redirects")
        except requests.exceptions.RequestException as e:
            print e
    if buycott_token != '':
        print("Looking up in Buycott")
        url = "https://www.buycott.com/api/v4/products/lookup"
        headers = {
        'Content-Type': 'application/json'
        }
        data={'barcode':upc,
              'access_token':buycott_token
             }
        try:
             r = requests.get(url=url, json=data, headers=headers)
             j = r.json()
             if r.status_code == 200:
                print("Buycott found it so now we're going to gather some info here and then add it to the system")
                name = j['products'][0]['product_name']
                description = j['products'][0]['product_description']
                #We now have what we need to add it to grocy so lets do that
                #Sometimes buycott returns a success but it never actually does anything so lets just make sure that we have something
                if name != '':
                    add_to_system(upc, name, description)
        except requests.exceptions.Timeout:
            print("The connection timed out")
        except requests.exceptions.TooManyRedirects:
            print ("Too many redirects")
        except requests.exceptions.RequestException as e:
            print e

    else:
        #This is a free service that limits you to 100 hits per day if we can't find it here we'll still create it in the system but it will be just a dummy entry
        print("Looking up in UPCItemDB")
        url = "https://api.upcitemdb.com/prod/trial/lookup?upc=%s" % (upc)
        headers = {
        'Content-Type': 'application/json',
        'cache-control': "no-cache"
        }
        try:
            r = requests.get(url=url, headers=headers)
            j = r.json()
            if r.status_code == 200:
                print("UPCItemDB found it so now we're going to gather some info here and then add it to the system")
                name = j['items'][0]['title']
                description = j['items'][0]['description']
                #We now have what we need to add it to grocy so lets do that
                add_to_system(upc, name, description)
            else:
                print ("The item with %s was not found so we're adding a dummy one") % (upc)
                name="The product was not found in the external sources you will need to fix %s" % (upc),
                description=''
                add_to_system(upc, name, description)
        except requests.exceptions.Timeout:
            print("The connection timed out")
        except requests.exceptions.TooManyRedirects:
            print ("Too many redirects")
        except requests.exceptions.RequestException as e:
            print e
    #By now we have our product added to the system.  We can now lookup our product_id again and then proceed with whatever it is we were doing
    if response_code != 204:
        #Something went wrong and the add the product was not added
        print("Adding the product with %s failed not sure why but it did") % (upc)
    product_id_lookup(upc)

#Rather than have this in every section of the UPC lookup we just have a function that we call for building the json for the api call to actually add it to the system
def add_to_system(upc, name, description):
    url = base_url+"/objects/products"
    data ={"name": name,
            "description": description,
            "barcode": upc,
            "location_id": 6,
            "qu_id_purchase": 1,
            "qu_id_stock":0,
            "qu_factor_purchase_to_stock": 1,
            "default_best_before_days": -1
        }
    grocy_api_call_post(url, data)
    if response_code==204:
        print("Just added %s to the system") % (name)
    if response_code !=204:
        print("Adding the product failed")

#This is a function that is referred to a lot through out the app so its easier for us to just use it as a function rather than type it out over and over
def grocy_api_call_post(url, data):
    headers = {
        'cache-control': "no-cache",
        'GROCY-API-KEY': GROCY_API
    }
    try:
        r = requests.post(url=url, json=data, headers=headers)
        r.status_code
        global response_code
        response_code = r.status_code
        print r.status_code
    except requests.exceptions.Timeout:
        print("The connection timed out")
    except requests.exceptions.TooManyRedirects:
        print ("Too many redirects")
    except requests.exceptions.RequestException as e:
        print e

for event in device.read_loop():
    if event.type == ecodes.EV_KEY:
        eventdata = categorize(event)
        if eventdata.keystate == 1: # Keydown
            scancode = eventdata.scancode
            if scancode == 28: # Enter
                print barcode
                if barcode != '':
                    if barcode == add_id and ADD == 0:
                        ADD = 1
                        barcode=''
                        print("Entering add mode")
                    elif barcode == add_id and ADD == 1:
                        ADD = 0
                        barcode=''
                        print("Entering consume mode")
                    elif ADD == 1:
                        upc=barcode
                        barcode=''
                        increase_inventory(upc)
                    elif ADD == 0:
                        upc=barcode
                        barcode=''
                        decrease_inventory(upc)
            else:
                key = scancodes.get(scancode, NOT_RECOGNIZED_KEY)
                barcode = barcode + key
                if key == NOT_RECOGNIZED_KEY:
                    print('unknown key, scancode=' + str(scancode))

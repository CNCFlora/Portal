import os

class BaseHandler():

    base_url= os.environ.get('BASE','')
    static_url= base_url+'/static'

    host = os.environ.get('HOST', 'cncflora.jbrj.gov.br')
    services_url = 'tcp://' + host + '/services'
    services= os.environ.get('SERVICES_PORT_8080_TCP', services_url)[6:]


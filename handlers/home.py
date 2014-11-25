import jinja2
import json
import requests
import urllib
from flask.views import MethodView
from flask import Flask, render_template, request, flash
from base import BaseHandler

class HomeHandler(MethodView, BaseHandler):

    def get(self):
          images=[]
          json_images = requests.get("http://cncflora.jbrj.gov.br/datahub/cncflora/_design/images/_view/tags?key=%22rdc%22&reduce=false")
          all_images = json.loads(json_images.text)
          for image in all_images["rows"]:
              img = {}
              cat = image["value"]["folder"]
              img["name"] = cat[cat.rfind(':',0,cat.rfind(':')) + 1:].replace(":"," ")
              img["img"]  = 'http://cncflora.jbrj.gov.br/datahub/cncflora/'+urllib.quote( image["id"].encode('utf8') )+'/'+urllib.quote( image["value"]["metadata"]["title"].encode('utf8') )

              #url = 'http://'+self.services+'/assessments/taxon/'+urllib.quote(img["name"])
              #r = requests.get(url)
              #j = json.loads(r.text);
              #img["category"] = j["category"]

              images.append(img)
          return  render_template('home.html',
          images=images,
          base_url=self.base_url,
          static_url= self.static_url)

import jinja2
import json
import requests
import urllib
import time
import datetime

from flask.views import MethodView
from flask import Flask, render_template, request, flash
from base import BaseHandler

class ProfileHandler(MethodView, BaseHandler):

    def get(self, language, name):
        server = self.services 
        url = 'http://'+server+'/assessments/taxon/'+urllib.quote(  name[:1].upper()+name[1:] )
        json_data = requests.get(url)
        assessment = json.loads(json_data.text)
        assessment["date"] = datetime.datetime.fromtimestamp(assessment["metadata"]["created"]).strftime('%d-%m-%Y')

        if assessment["rationale"][0] == '?':
          assessment["rationale"] = assessment["rationale"][1:]

        url = 'http://'+server+'/profiles/taxon/'+urllib.quote(  name[:1].upper()+name[1:] )
        json_data = requests.get(url)
        profile = json.loads(json_data.text)

        references=[]
        if 'references' in assessment.keys():
            references=assessment[ "references" ],

        return render_template(
                'profile.html',
                language=language,
                base_url=self.base_url,
                static_url=self.static_url,
                references=references,
                profile=profile,
                assessment=assessment)

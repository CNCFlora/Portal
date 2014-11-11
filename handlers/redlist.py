from flask.views import MethodView
from flask import Flask, render_template, request, flash
from base import BaseHandler

import requests
import jinja2
import json

class RedlistHandler(MethodView, BaseHandler):

    def get(self, language, family):
        family_description = []
        if family:
            json_family = requests.get('http://cncflora.jbrj.gov.br/services/assessments/family/'+family)
            family_description = json.loads(json_family.text)
        else:
            family = {}
        json_data = requests.get('http://cncflora.jbrj.gov.br/services/assessments/families')
        families = json.loads(json_data.text)
        return render_template(
                'redlist.html',
                language=language,
                base_url=self.base_url,
                static_url=self.static_url,
                families=sorted(families),
                family_description=family_description,
                family=family
                )

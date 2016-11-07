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
        #print assessment["taxon"]
        assessment["date"] = datetime.datetime.fromtimestamp(assessment["metadata"]["created"]).strftime('%d-%m-%Y')

        if assessment["rationale"][0] == '?':
          assessment["rationale"] = assessment["rationale"][1:]

        url = 'http://'+server+'/profiles/taxon/'+urllib.quote(  name[:1].upper()+name[1:] )

        json_data = requests.get(url)
        profile = json.loads(json_data.text)

        last_modified_by = profile["metadata"]["contributor"].split(";")[0]

        last_modified_at = datetime.datetime.fromtimestamp(profile["metadata"]["modified"]).strftime('%d/%m/%Y - %H:%M:%S')

        url = 'http://'+server+'/occurrences/scientificName/'+urllib.quote(  name[:1].upper()+name[1:] )
        json_data = requests.get(url)
        occurrences = json.loads(json_data.text)
        occurrence = []
        georeferencedBy = ""
        specialist = ""
        analyst = profile["metadata"]["creator"]

        if "metadata" in profile and "validatedBy" in profile["metadata"]:
            analyst = profile["metadata"]["contributor"]
            georeferencedBy = profile["metadata"]["georeferencedBy"]
            specialist = profile["metadata"]["validatedBy"]
        else:
            for occ in occurrences:
                if "georeferencedBy" in occ:
                    if georeferencedBy.find(occ["georeferencedBy"]) == -1:
                        if georeferencedBy=="":
                            georeferencedBy = occ["georeferencedBy"]
                        else:
                            georeferencedBy += ", " + occ["georeferencedBy"]
                if "validation" in occ:
                    occ = occ["validation"]
                    if "by" in occ:
                        if occ["by"] is not None:
                            if specialist.find(occ["by"]) == -1:
                                if specialist=="":
                                    specialist = occ["by"]
                                else:
                                    specialist += ", " + occ["by"]

        references=[]
        if 'references' in assessment.keys():
            references=assessment[ "references" ],

        references_str=[]
        for ref in references:
            for ref0 in ref:
                references_str.append(" "+ref0)
                # references_str += " "+ref0

        return render_template(
                'profile.html',
                language=language,
                base_url=self.base_url,
                static_url=self.static_url,
                references_str=references_str,
                profile=profile,
                assessment=assessment,
                georeferencedBy=georeferencedBy,
                specialist=specialist,
                last_modified_by=last_modified_by,
                last_modified_at=last_modified_at,
                analyst=analyst)

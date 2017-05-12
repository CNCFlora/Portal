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
        profile2 = "";
        profile3 = "";
        last_modified_by2 = "";
        last_modified_by3 = "";
        last_modified_at2 = "";
        last_modified_at3 = "";
        assessment2 = "";
        assessment3 = "";
        analyst2 = "";
        analyst3 = "";
        georeferencedBy2 = "";
        georeferencedBy3 = "";
        specialist2 = "";
        specialist3 = "";
        references_str3 = "";
        references_str2 = "";
        profile = "";
        last_modified_by = "";
        last_modified_at = "";
        analyst = "";
        georeferencedBy = "";
        specialist = "";
        references_str = "";

        qtd_names = len(name.split(" "))

        #avoid two different species with same gender
        if(qtd_names > 1):
            #reavaliada
            url = 'http://'+server+'/assessments/2taxon/'+urllib.quote(  name[:1].upper()+name[1:] )
            json_data = requests.get(url)
            assessment2 = json.loads(json_data.text)

            if assessment2:
                assessment2["date"] = datetime.datetime.fromtimestamp(int(assessment2["metadata"]["created"])).strftime('%d-%m-%Y')

                if assessment2["rationale"][0] == '?':
                  assessment2["rationale"] = assessment2["rationale"][1:]

                url = 'http://'+server+'/profiles/2taxon/'+urllib.quote(  name[:1].upper()+name[1:] )

                json_data = requests.get(url)
                profile2 = json.loads(json_data.text)

                if profile2:
                    last_modified_by2 = profile2["metadata"]["contributor"].split(";")[0]

                    last_modified_at2 = datetime.datetime.fromtimestamp(profile2["metadata"]["modified"]).strftime('%d/%m/%Y - %H:%M:%S')

                    url = 'http://'+server+'/occurrences/2scientificName/'+urllib.quote(  name[:1].upper()+name[1:] )
                    json_data = requests.get(url)
                    occurrences2 = json.loads(json_data.text)
                    occurrence = []
                    georeferencedBy2 = ""
                    specialist2 = ""
                    analyst2 = profile2["metadata"]["creator"]

                    if "metadata" in profile2 and "validatedBy" in profile2["metadata"]:
                        analyst2 = profile2["metadata"]["contributor"]
                        georeferencedBy2 = profile2["metadata"]["georeferencedBy"]
                        specialist2 = profile2["metadata"]["validatedBy"]
                    else:
                        for occ in occurrences2:
                            if "georeferencedBy" in occ:
                                if georeferencedBy2.find(occ["georeferencedBy"]) == -1:
                                    if georeferencedBy2=="":
                                        georeferencedBy2 = occ["georeferencedBy"]
                                    else:
                                        georeferencedBy2 += ", " + occ["georeferencedBy"]
                            if "validation" in occ:
                                occ = occ["validation"]
                                if "by" in occ:
                                    if occ["by"] is not None:
                                        if specialist2.find(occ["by"]) == -1:
                                            if specialist2=="":
                                                specialist2 = occ["by"]
                                            else:
                                                specialist2 += ", " + occ["by"]

                    references2=[]
                    if 'references' in assessment2.keys():
                        references2=assessment2[ "references" ],

                    references_str2=[]
                    for ref in references2:
                        for ref0 in ref:
                            references_str2.append(" "+ref0)
                            # references_str += " "+ref0

            #especie nova publicada
            url = 'http://'+server+'/assessments/3taxon/'+urllib.quote(  name[:1].upper()+name[1:] )
            json_data = requests.get(url)
            assessment3 = json.loads(json_data.text)

            if assessment3 and assessment3["public"]:
                assessment3["date"] = datetime.datetime.fromtimestamp(int(assessment3["metadata"]["created"])).strftime('%d-%m-%Y')

                if assessment3["rationale"][0] == '?':
                  assessment3["rationale"] = assessment3["rationale"][1:]

                url = 'http://'+server+'/profiles/3taxon/'+urllib.quote(  name[:1].upper()+name[1:] )

                json_data = requests.get(url)
                profile3 = json.loads(json_data.text)

                last_modified_by3 = profile3["metadata"]["contributor"].split(";")[0]

                last_modified_at3 = datetime.datetime.fromtimestamp(profile3["metadata"]["modified"]).strftime('%d/%m/%Y - %H:%M:%S')

                url = 'http://'+server+'/occurrences/3scientificName/'+urllib.quote(name[:1].upper()+name[1:])
                json_data = requests.get(url)
                occurrences3 = json.loads(json_data.text)
                occurrence = []
                georeferencedBy3 = ""
                specialist3 = ""
                analyst3 = profile3["metadata"]["creator"]

                if "metadata" in profile3 and "validatedBy" in profile3["metadata"]:
                    analyst3 = profile3["metadata"]["contributor"]
                    georeferencedBy3 = profile3["metadata"]["georeferencedBy"]
                    specialist3 = profile3["metadata"]["validatedBy"]
                else:
                    for occ in occurrences3:
                        if "georeferencedBy" in occ:
                            if georeferencedBy3.find(occ["georeferencedBy"]) == -1:
                                if georeferencedBy3=="":
                                    georeferencedBy3 = occ["georeferencedBy"]
                                else:
                                    georeferencedBy3 += ", " + occ["georeferencedBy"]
                        if "validation" in occ:
                            occ = occ["validation"]
                            if "by" in occ:
                                if occ["by"] is not None:
                                    if specialist3.find(occ["by"]) == -1:
                                        if specialist3=="":
                                            specialist3 = occ["by"]
                                        else:
                                            specialist3 += ", " + occ["by"]

                references3=[]
                if 'references' in assessment3.keys():
                    references3=assessment3[ "references" ],

                references_str3=[]
                for ref in references3:
                    for ref0 in ref:
                        references_str3.append(" "+ref0)
                        # references_str += " "+ref0

        if assessment:
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

            if assessment3 and assessment["rationale"] == assessment3["rationale"]:
                assessment = "";

        return render_template(
                'profile.html',
                language=language,
                base_url=self.base_url,
                static_url=self.static_url,
                references_str=references_str,
                references_str2=references_str2,
                references_str3=references_str3,
                profile=profile,
                profile2=profile2,
                profile3=profile3,
                assessment=assessment,
                #waiting for publication
                #assessment2=assessment2,
                assessment3=assessment3,
                georeferencedBy=georeferencedBy,
                georeferencedBy2=georeferencedBy2,
                georeferencedBy3=georeferencedBy3,
                specialist=specialist,
                specialist2=specialist2,
                specialist3=specialist3,
                last_modified_by=last_modified_by,
                last_modified_by2=last_modified_by2,
                last_modified_by3=last_modified_by3,
                last_modified_at=last_modified_at,
                last_modified_at2=last_modified_at2,
                last_modified_at3=last_modified_at3,
                analyst=analyst,
                analyst2=analyst2,
                analyst3=analyst3)

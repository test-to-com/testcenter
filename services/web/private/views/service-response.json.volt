{# Test Center - Compliance Testing Application (Web Services)
 # Copyright (C) 2012-2015 Paulo Ferreira <pf at sourcenotes.org>
 #
 # This program is free software: you can redistribute it and/or modify
 # it under the terms of the GNU Affero General Public License as
 # published by the Free Software Foundation, either version 3 of the
 # License, or (at your option) any later version.
 #
 # This program is distributed in the hope that it will be useful,
 # but WITHOUT ANY WARRANTY; without even the implied warranty of
 # MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 # GNU Affero General Public License for more details.
 #
 # You should have received a copy of the GNU Affero General Public License
 # along with this program.  If not, see <http://www.gnu.org/licenses/>.
 #
 #}

{
  {# STANDARD JSON Message Reply
   # If version not provided a version 1.0.0 will be assumed
   #}
  "version" : {
  {% if version is defined %}
    "major": {{ version['major'] }}, "minor": {{ version['minor'] }}, "build": {{ version['build'] }}
  {% else %}
    {# No Version Specified - Default to 1.0.0 #}
    "major": 1, "minor": 0, "build": 0
  {% endif %}
  },
  {# JSON Message Reply Error
   # If no error code provided a code of 0 with a Message of "Ok" will be assumed
   #}
  "error" : {
  {% if error is defined %}
    "code": {{ error['code'] }},
    "message":
      {% autoescape false %}
        {{ error['message']|json_encode }}
      {% endautoescape %}
  {% else %}
    "code": 0, "message": {{ "GENERAL:RESPONSE:OK"|_|json_encode }}
  {% endif %}
  }
  {# Message Return Value
   # If none provided if provided, then NULL will be assumed
   #}
  {% if results is defined %}
  ,"return" :
    {% autoescape false %}
      {{ results|json_encode }}
    {% endautoescape %}
  {% endif %}
}

import urllib.request
import urllib.error
import json

key = 'AIzaSyDOoxQfiguM4i6oVC68GdSCiS3TpSuNOGg'
paths = [
    'v1/alpha/models/gemini-1.0:generate',
    'v1/alpha/models/gemini-1.0:predict',
    'v1/alpha2/models/gemini-1.0:generate',
    'v1/beta2/models/gemini-1.0:generate',
    'v1/beta3/models/gemini-1.0:generate',
    'v1/locations/us-central1/models/gemini-1.0:generate',
    'v1/alpha/locations/us-central1/models/gemini-1.0:generate',
    'v1/alpha2/locations/us-central1/models/gemini-1.0:generate',
    'v1/beta2/locations/us-central1/models/gemini-1.0:generate',
]

for path in paths:
    url = f'https://gemini.googleapis.com/{path}?key={key}'
    print('URL=', url)
    payload = json.dumps({'prompt': {'messages': [{'author': 'system', 'content': [{'type': 'text', 'text': 'Say hi.'}] }]}}).encode('utf-8')
    req = urllib.request.Request(url, data=payload, headers={'Content-Type': 'application/json'})
    try:
        with urllib.request.urlopen(req) as r:
            print('  OK', r.status)
            print('  ', r.read().decode('utf-8')[:400])
    except urllib.error.HTTPError as e:
        print('  ERR', e.code)
        print('  ', e.read().decode('utf-8', errors='replace')[:400])
    except Exception as e:
        print('  EX', e)
    print('\n' + '-'*60 + '\n')

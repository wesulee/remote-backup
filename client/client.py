'''
Python client for remote backup
'''
import requests


remoteAddress = "http://localhost/server/upload_file.php"

def uploadFile(filePath, remoteAddress):
    '''Upload a file using POST to remoteAddress'''
    files = {'file': (filePath, open(filePath, 'rb'))}
    payload = {'full_path': filePath}
    r = requests.post(remoteAddress, files=files, data=payload)
    return r


result = uploadFile('folder/test_file.txt', remoteAddress)
print result.text

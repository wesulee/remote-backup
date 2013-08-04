'''
Python client for remote backup
'''
import os
import requests
import itertools
import json


class Server(object):
    '''All interactions with server use this class'''
    def __init__(self):
        self.server = 'http://localhost/server/api/api.php'
        self.maxUploadSize = 1024 * 1024 * 2
        self.timeout = 5    # seconds for connection process
        self.authenticate()

    def do(self, action, files=None, data={}, stream=False):
        '''Execute the action'''
        data['action'] = action
        prepped = requests.Request('POST', self.server, files=files,
                                   data=data, cookies=self.s.cookies).prepare()
        resp = self.s.send(prepped, stream=stream, verify=False,
                           timeout=self.timeout)
        return resp

    def authenticate(self):
        self.s = requests.Session()
        payload = {'username':'test1', 'password':'test123'}
        r = self.do('authenticate', data=payload)
        resp = self.decodeResponse(r.text)
        if 'error' in resp:
            raise 'AuthenticationError'

    def uploadFile(self, f):
        '''Upload a file using POST'''
        if f.size > self.maxUploadSize:
            return self.multiUploadFile(f)
        files = {'file':(f.path, open(f.path, 'rb'))}
        payload = {'full_path':f.fpath}
        r = self.do('uploadfile', files=files, data=payload)
        return r.text

    def multiUploadFile(self, f):
        '''Upload a file that exceeds the server limit'''
        defaultPayload = {'full_path':f.fpath, 'multi':1}
        part = 1
        if f.size % self.maxUploadSize == 0:
            endPart = f.size / self.maxUploadSize
        else:
            endPart = f.size / self.maxUploadSize + 1
        F = open(f.path, 'rb')
        chunk = F.read(self.maxUploadSize)
        while chunk:
            files = {'file':(f.path, chunk)}
            payload = {'part':part}
            payload.update(defaultPayload)
            if part == endPart:
                payload['last'] = 1
            r = self.do('multiuploadfile', files=files, data=payload)
            resp = self.decodeResponse(r.text)
            if 'error' in resp:
                return resp
            if part == endPart:
                F.close()
                return resp
            else:
                part += 1
                chunk = F.read(self.maxUploadSize)
    
    def downloadFile(self, localPath, remotePath):
        '''download url from server to localPath'''
        with open(localPath, 'wb') as f:
            payload = {'action':'downloadfile', 'path': remotePath}
            req = requests.post(self.server, data=payload, stream=True,
                               cookies=self.s.cookies)
            for block in req.iter_content(1024 * 100):
                if not block:
                    break
                f.write(block)

    def uploadFolder(self, folderPath):
        '''Upload all files in folder'''
        if not os.path.isdir(folderPath):
            if os.path.exists(folderPath):
                print 'Path is not a folder'
            else:
                print 'Folder does not exist'
            return
        for filePath in allFiles(folderPath):
            self.uploadFile(File(filePath))

    def decodeResponse(self, response):
        '''deserialize JSON string'''
        return json.loads(response)


class File(object):
    def __init__(self, path):
        self.path = path                        # local path
        self.fpath = self.formatUploadPath()    # path used for server
        fileInfo = os.stat(path)
        self.size = fileInfo.st_size            # file size in bytes
        self.modified = fileInfo.st_mtime       # modified time

    def formatUploadPath(self):
        '''correct path for full_path parameter when uploading'''
        path = self.path.replace('\\', '/')
        return path[indexExclude(path, '/'):]

    def __str__(self):
        return 'Filepath: '+self.path


class RootFolder(object):
    '''root folder where all contents will be uploaded'''
    def __init__(self, path):
        self.path = path
        self.server = Server()


def allFiles(directory):
    '''gets a list of all files in directory'''
    files = []
    for dirPath, dirNames, fileNames in os.walk(directory):
        files.extend([os.path.join(dirPath, fName) for fName in fileNames])
    return files


def indexExclude(string, excludeString):
    '''index of the first character in string not in excludeString'''
    excludeSet = set(excludeString)
    for i, c in itertools.izip(xrange(len(string)), string):
        if c not in excludeSet:
            return i
    return -1

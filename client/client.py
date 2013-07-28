'''
Python client for remote backup
'''
import os
import requests
import itertools



class Server(object):
    '''all interactions with server use this class'''
    def __init__(self):
        self.serverBase = 'http://localhost/server/'
        self.maxUploadSize = 1024 * 1024 * 2
        self.actions = {'ufi':'upload_file.php', 'df':'download_file.php'}

    def actionURL(self, action):
        '''generates the url for an action'''
        return self.serverBase + self.actions[action]

    def uploadFile(self, f):
        '''Upload a file using POST'''
        if f.size > self.maxUploadSize:
            return self.multiUploadFile(f)
        url = self.actionURL('ufi')
        files = {'file':(f.path, open(f.path, 'rb'))}
        payload = {'full_path':f.fpath}
        r = requests.post(url, files=files, data=payload)
        return r.text

    def multiUploadFile(self, f):
        '''upload a file that exceeds the server limit'''
        url = self.actionURL('ufi')
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
            r = requests.post(url, files=files, data=payload)
            if part == endPart:
                F.close()
                return
            else:
                part += 1
                chunk = F.read(self.maxUploadSize)
    
    def downloadFile(self, localPath, remotePath):
        '''download url from server to localPath'''
        url = self.actionURL('df')
        with open(localPath, 'wb') as f:
            payload = {'path': remotePath}
            req = requests.get(url, params=payload, stream=True)
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
        url = self.actionURL('ufi')
        files = allFiles(folderPath)
        for filePath in files:
            self.uploadFile(File(filePath))


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

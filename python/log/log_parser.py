# -*- coding: utf-8 -*-

import re


def parse(filename):
    'Return tuple of dictionaries containing file data.'

    def make_entry(x):

        datetime = x.group('time')

        MonthDict = {"Jan": '01',
                     "Feb": '02',
                     "Mar": '03',
                     "Apr": '04',
                     "May": '05',
                     "Jun": '06',
                     "Jul": '07',
                     "Aug": '08',
                     "Sep": '09',
                     "Oct": '10',
                     "Nov": '11',
                     "Dec": '12'}

        datetime = datetime.replace(':', ' ', 1)
        splitted = datetime.split(' ')
        olddate = splitted[0].split('/')
        olddate[1] = MonthDict[olddate[1]]
        newdate = olddate[2] + '-' + olddate[1] + '-' + olddate[0] + ' ' + splitted[1]

        agent = x.group('agent').replace("\\", "").replace("'", "")
        referral = x.group('referral').replace("\\", "").replace("'", "")
        uri = x.group('uri').replace("'", "").replace("\\", "")

        return {
            'ip': x.group('ip'),
            'uri': uri,
            'user': x.group('user'),
            'method': x.group('method'),
            'request_time': newdate,
            'code': x.group('code'),
            'referral': referral,
            'agent': agent,
            'zone': x.group('zone'),
            'bytes': x.group('bytes'),
        }

    log_re = '(?P<ip>[.:0-9a-fA-F]+) - (?P<user>.*?) \[(?P<time>.*?) (?P<zone>.*?)\] "(?P<method>.*?) (?P<uri>.*?) HTTP/1.\d" (?P<code>\d+) (?P<bytes>\d+) "(?P<referral>.*?)" "(?P<agent>.*?)"'
    search = re.compile(log_re).search
    matches = (search(line) for line in open(filename))
    return (make_entry(x) for x in matches if x)


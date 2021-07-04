# -*- coding: utf-8 -*-

import pymysql


def insert(database, table, user, password, data):
    con = pymysql.connect(host='localhost',
                          user=user,
                          password=password,
                          database=database,
                          charset='utf8mb4',
                          cursorclass=pymysql.cursors.DictCursor)



    with con:
        with con.cursor() as cursor:
            data = list(data)
            columns = list(data[0].keys())
            colStr = ""
            for key in columns:
                colStr += key + ', '
            colStr = colStr[:-2]
            for entry in data:
                valStr = ""
                for key in columns:
                    if key in {'code', 'zone', 'bytes'}:
                        valStr += str(entry[key]) + ', '
                    else:
                        valStr += "'" + str(entry[key]) + "', "
                valStr = valStr[:-2]
                sql = "INSERT INTO " + table + " (" + colStr + ") VALUES (" + valStr + ");"
                cursor.execute(sql)
            con.commit()

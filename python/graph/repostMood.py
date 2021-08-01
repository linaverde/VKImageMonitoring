# -*- coding: utf-8 -*-

from argparse import ArgumentParser
import matplotlib.pyplot as plt
import pymysql
import json


def main():
    # Обязательные параметры
    p = ArgumentParser(description="Print graphs for comments mood")
    p.add_argument('id', type=str, help="monitoring record info database id")

    # Настройка базы данных
    p.add_argument('-db', '--database',
                   type=str, help='Flag to add parsed data to given database')
    p.add_argument('-u', '--user',
                   type=str, help='Username to connect to database')
    p.add_argument('-p', '--password',
                   type=str, help='Password to connect to database')

    options = p.parse_args()

    id = options.id
    if id is None:
        print("ERROR NO ID")
    db = options.database
    user = options.user
    password = options.password

    if db is not None:
        if user is None or password is None:
            p.error("You need specify table, user and password to use database")
        findRow(id, db, user, password)


def findRow(id, db, user, password):
    con = pymysql.connect(host='localhost',
                          user=user,
                          password=password,
                          database=db,
                          charset='utf8mb4',
                          cursorclass=pymysql.cursors.DictCursor)

    with con:
        cur = con.cursor()
        cur.execute(
            'SELECT * FROM monitoring_record '
            'JOIN publication ON publication.record_id = monitoring_record.id  '
            'JOIN repost ON repost.publication_id = publication.id '
            'WHERE monitoring_record.id= ' + id + ';')
        reposts = cur.fetchall()

        positive = 0
        negative = 0
        neutral = 0

        for repost in reposts:
            mood = repost['mood']
            if mood == "+":
                positive += 1
            elif mood == '-':
                negative += 1
            else:
                neutral += 1

        values = []
        labels = []
        if positive > 0:
            values.append((positive/len(reposts))*100)
            labels.append("Позитивные")

        if negative > 0:
            values.append((negative/len(reposts))*100)
            labels.append("Негативные")

        if neutral > 0:
            values.append((neutral/len(reposts))*100)
            labels.append("Нейтральные")

        fig2, ax2 = plt.subplots()
        ax2.pie(values, labels=labels, autopct='%1.1f%%')
        fig2.savefig('/var/www/image/public/graphs/reposts/' + id + '_mood.png')


if __name__ == '__main__':
    main()

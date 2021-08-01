# -*- coding: utf-8 -*-

from argparse import ArgumentParser
import matplotlib.pyplot as plt
import pymysql
import json


def main():
    # Обязательные параметры
    p = ArgumentParser(description="Print graphs for subscriber info")
    p.add_argument('id', type=str, help="subscriber info database id")

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
        findRow(id, db, 'subsctiber_info', user, password)


def findRow(id, db, table, user, password):
    con = pymysql.connect(host='localhost',
                          user=user,
                          password=password,
                          database=db,
                          charset='utf8mb4',
                          cursorclass=pymysql.cursors.DictCursor)

    with con:
        cur = con.cursor()
        cur.execute('SELECT * FROM ' + table + ' WHERE id= ' + id + ';')
        subscribers = cur.fetchone()

        count = subscribers['count']
        fcount = subscribers['female_count']
        mcount = subscribers['male_count']

        notset = ((count - (fcount + mcount)) / count) * 100
        fcount = (fcount / count) * 100
        mcount = (mcount / count) * 100

        labels = ['Женщины',
                  'Мужчины']
        values = [fcount,
                  mcount]
        if notset > 0:
            labels.append('Пол не указан')
            values.append(notset)

        fig1, ax1 = plt.subplots()
        ax1.pie(values, labels=labels, autopct='%1.1f%%')
        fig1.savefig('/var/www/image/public/graphs/subscribers/' + id + '_gender.png')

        city = subscribers['city']
        country = subscribers['country']

        if city is not None and country is not None:
            citycount = (subscribers['expected_city_count'] / count) * 100
            countrycount = ((subscribers['expected_country_count'] / count) * 100) - citycount
            notset = ((count - subscribers['expected_country_count']) / count) * 100

            labels = [
                city,
                country
            ]

            values = [
                citycount,
                countrycount,
            ]

            if notset > 0:
                labels.append('Другая страна')
                values.append(notset)

            fig2, ax2 = plt.subplots()
            ax2.pie(values, labels=labels, autopct='%1.1f%%')
            fig2.savefig('/var/www/image/public/graphs/subscribers/' + id + '_area.png')
        elif city is not None and country is None:
            citycount = (subscribers['expected_city_count'] / count) * 100
            notset = ((count - subscribers['expected_city_count']) / count) * 100

            labels = [
                city,
            ]

            values = [
                citycount,
            ]

            if notset > 0:
                labels.append('Другие города')
                values.append(notset)

            fig2, ax2 = plt.subplots()
            ax2.pie(values, labels=labels, autopct='%1.1f%%')
            fig2.savefig('/var/www/image/public/graphs/subscribers/' + id + '_area.png')
        elif city is None and country is not None:
            countrycount = ((subscribers['expected_country_count'] / count) * 100) - citycount
            notset = ((count - subscribers['expected_country_count']) / count) * 100

            labels = [
                country
            ]

            values = [
                countrycount,
            ]

            if notset > 0:
                labels.append('Другая страна')
                values.append(notset)

            fig2, ax2 = plt.subplots()
            ax2.pie(values, labels=labels, autopct='%1.1f%%')
            fig2.savefig('/var/www/image/public/graphs/subscribers/' + id + '_area.png')

        agecount = (subscribers['expected_age_count'] / count) * 100
        smallerage = (subscribers['smaller_age_count'] / count) * 100
        biggerage = (subscribers['bigger_age_count'] / count) * 100
        notagecount = ((count - (subscribers['expected_age_count'] + subscribers['smaller_age_count'] + subscribers[
            'bigger_age_count'])) / count) * 100

        labels = []
        values = []

        if agecount > 0:
            labels.append(str(subscribers['min_age']) + '-' + str(subscribers['max_age']) + ' лет', )
            values.append(agecount)

        if notset > 0:
            labels.append('Возраст не указан')
            values.append(notset)

        if smallerage > 0:
            labels.append('Младше, чем ' + str(subscribers['min_age']))
            values.append(smallerage)

        if biggerage > 0:
            labels.append('Старше, чем ' + str(subscribers['max_age']))
            values.append(biggerage)

        fig2, ax2 = plt.subplots()
        ax2.pie(values, labels=labels, autopct='%1.1f%%')
        fig2.savefig('/var/www/image/public/graphs/subscribers/' + id + '_age.png')


if __name__ == '__main__':
    main()

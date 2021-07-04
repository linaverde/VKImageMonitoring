# -*- coding: utf-8 -*-

from argparse import ArgumentParser
import log_parser
import printer
import database


def main():
    # Обязательные параметры
    p = ArgumentParser(description="Parse log file")
    p.add_argument('file', type=str, help="Path to log file")

    # Настройка базы данных
    p.add_argument('-db', '--database',
                   type=str, help='Flag to add parsed data to given database')
    p.add_argument('-t', '--table',
                   type=str, help='Flag to add parsed data to given database table')
    p.add_argument('-u', '--user',
                   type=str, help='Username to connect to database')
    p.add_argument('-p', '--password',
                   type=str, help='Password to connect to database')

    # Изначальные параметры
    p.add_argument('-o', '--output',
                   type=str, help='What part of logs must be in output for console')
    p.add_argument('-c', '--cutoff', dest='cutoff',
                   help="CUTOFF for minimum hits",
                   metavar="CUTOFF")
    p.add_argument('-q', '--quantity', dest='quantity',
                   help="QUANTITY of results to return",
                   metavar="QUANTITY")
    options = p.parse_args()

    filename = options.file
    if filename is None:
        print("ERROR NO FILE")
    db = options.database
    table = options.table
    user = options.user
    password = options.password
    report_type = options.output

    entries = log_parser.parse(filename)


    if report_type is not None:
        cutoff = int(options.cutoff) if options.cutoff else None
        qty = int(options.quantity) if options.quantity else None
        if report_type == 'subscriptions':
            printer.subscriptions(entries, cutoff=cutoff, quantity=qty)
        elif report_type in ['uri', 'time', 'status_code', 'agent', 'referral']:
            printer.generic_report_for_key(entries, report_type, cutoff, qty)
        else:
            p.error("specified an invalid report type")

    if db is not None:
        if user is None or password is None or table is None:
            p.error("You need specify table, user and password to use database")
        database.insert(str(db), "log" , str(user), str(password), entries)

if __name__ == '__main__':
    main()

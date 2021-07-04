# -*- coding: utf-8 -*-

from functools import reduce
from operator import itemgetter
import re, operator


def count_value(lst, key):
    d = {}
    for obj in lst:
        val = obj[key]
        if val in d:
            d[val] = d[val] + 1
        else:
            d[val] = 1
    return d.items()


def restrict(lst, cutoff, count):
    'Restrict the list by minimum value or count.'
    if cutoff:
        lst = (x for x in lst if x[1] > cutoff)
    if count:
        lst = lst[:count]
    return lst


def print_results(lst):
    for item in lst:
        print("%50s %10s" % (item[0], item[1]))


def generic_report_for_key(entries, key, cutoff, quantity):
    'Handles creating generic reports.'
    lst = count_value(entries, key)
    lst = sorted(lst, key=itemgetter(1), reverse=True)
    lst = restrict(lst, cutoff, quantity)
    print_results(lst)


def subscriptions(entries, cutoff, quantity):
    'Creates a custom report for determining number of subscribers per feed.'
    entries = (x for x in entries if 'ubscriber' in x['agent'])
    feeds = {}
    for obj in entries:
        uri = obj['uri']
        agent = obj['agent']
        if uri in feeds:
            feeds[uri].append(agent)
        else:
            feeds[uri] = [agent]

    feed_re = '(?P<name>.*?) \(.*?; (?P<count>\d+) subscribers?(; .*?)?\)'
    feed_re2 = '(?P<name>.*?);? (?P<count>\d+) (S|s)ubscribers?'
    search = re.compile(feed_re).search
    search2 = re.compile(feed_re2).search

    # remove duplicate subscriptions to avoid
    # double counting
    results = []
    for key, readers in feeds.iteritems():
        sources = {}
        for reader in readers:
            m = search(reader)
            if not m:
                m = search2(reader)
            if m:
                name = m.group('name')
                count = int(m.group('count'))
                if name in sources:
                    if sources[name] < count:
                        sources[name] = count
                else:
                    sources[name] = count

        # sum subscription totals
        vals = (val for key, val in sources.iteritems())
        sum = reduce(operator.add, vals, 0)

        # there can be false positives with weird user agents,
        # but they won't have any subscribers listed, so we
        # filter them out here
        if sum > 0:
            results.append((key, sum))

    results = sorted(results, key=itemgetter(1), reverse=True)
    results = restrict(results, cutoff, quantity)
    print_results(results)

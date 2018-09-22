#!/bin/sh

/usr/bin/mysqldump --defaults-extra-file=/home/whuang/sdc/bapa.rocks/mysqlbapa.cnf baparocks --ignore-table=baparocks.audit_entry --ignore-table=baparocks.audit_error --ignore-table=baparocks.audit_javascript --ignore-table=baparocks.audit_mail --ignore-table=baparocks.audit_trail --ignore-table=baparocks.audit_data > $1
